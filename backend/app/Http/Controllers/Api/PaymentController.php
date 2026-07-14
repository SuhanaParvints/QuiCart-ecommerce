<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Create a Razorpay order.
     *
     * The amount is calculated from products stored in MySQL.
     * The browser-provided price is not trusted.
     */
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',

            'items.*.product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:1000',
            'location' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'pincode' => 'required|string|max:20',
        ]);

        $trustedItems = [];
        $totalAmount = 0;

        foreach ($validated['items'] as $cartItem) {
            $product = Product::findOrFail(
                $cartItem['product_id']
            );

            $quantity = (int) $cartItem['quantity'];

            if ($product->status === 'sold_out') {
                throw ValidationException::withMessages([
                    'items' => [
                        "{$product->name} is currently sold out.",
                    ],
                ]);
            }

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'items' => [
                        "Only {$product->stock} unit(s) of {$product->name} are available.",
                    ],
                ]);
            }

            $unitPrice = (float) $product->price;
            $lineTotal = $unitPrice * $quantity;

            $totalAmount += $lineTotal;

            $trustedItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_image' => $product->image,
                'price' => $unitPrice,
                'quantity' => $quantity,
                'total_price' => $lineTotal,
            ];
        }

        /*
         * Razorpay requires INR amounts in paise.
         * ₹1,299 becomes 129900.
         */
        $amountInPaise = (int) round($totalAmount * 100);

        if ($amountInPaise < 100) {
            throw ValidationException::withMessages([
                'amount' => [
                    'The payment amount must be at least ₹1.',
                ],
            ]);
        }

        $receipt = 'qc_' . Str::lower(
            Str::random(20)
        );

        $response = Http::withBasicAuth(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        )->acceptJson()->post(
            'https://api.razorpay.com/v1/orders',
            [
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'receipt' => $receipt,
                'notes' => [
                    'user_id' => (string) $request->user()->id,
                    'store' => 'QuiCart',
                ],
            ]
        );

        if ($response->failed()) {
            return response()->json([
                'message' => 'Unable to create Razorpay order.',
                'razorpay_error' => $response->json(),
            ], 502);
        }

        $razorpayOrder = $response->json();

        /*
         * Keep the trusted checkout details temporarily.
         * These details are used after payment verification.
         */
        Cache::put(
            'razorpay_order:' . $razorpayOrder['id'],
            [
                'user_id' => $request->user()->id,
                'amount' => $totalAmount,
                'amount_in_paise' => $amountInPaise,
                'currency' => 'INR',

                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'location' => $validated['location'],
                'state' => $validated['state'],
                'pincode' => $validated['pincode'],

                'items' => $trustedItems,
            ],
            now()->addMinutes(30)
        );

        return response()->json([
            'key' => config('services.razorpay.key'),
            'order_id' => $razorpayOrder['id'],
            'amount' => $razorpayOrder['amount'],
            'currency' => $razorpayOrder['currency'],
        ]);
    }

    /**
     * Verify Razorpay signature and create QuiCart orders.
     */
    public function verifyPayment(Request $request)
    {
        $validated = $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $cacheKey =
            'razorpay_order:' .
            $validated['razorpay_order_id'];

        $pendingCheckout = Cache::get($cacheKey);

        if (! $pendingCheckout) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Payment session expired or was not found.',
            ], 422);
        }

        if (
            (int) $pendingCheckout['user_id'] !==
            (int) $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'This payment does not belong to this user.',
            ], 403);
        }

        /*
         * Signature format:
         * HMAC_SHA256(order_id|payment_id, secret)
         *
         * The trusted order ID is the one created and stored
         * by our Laravel server.
         */
        $generatedSignature = hash_hmac(
            'sha256',
            $validated['razorpay_order_id'] .
                '|' .
                $validated['razorpay_payment_id'],
            config('services.razorpay.secret')
        );

        if (
            ! hash_equals(
                $generatedSignature,
                $validated['razorpay_signature']
            )
        ) {
            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Invalid payment signature.',
            ], 422);
        }

        /*
         * Prevent creating the same orders twice.
         */
        $existingPayment = Payment::where(
            'razorpay_payment_id',
            $validated['razorpay_payment_id']
        )->first();

        if ($existingPayment) {
            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => 'Payment was already verified.',
                'payment' => $existingPayment,
            ]);
        }

        try {
            $result = DB::transaction(function () use (
                $validated,
                $pendingCheckout
            ) {
                $createdOrderIds = [];

                foreach ($pendingCheckout['items'] as $item) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->findOrFail($item['product_id']);

                    $quantity = (int) $item['quantity'];

                    if ($product->stock < $quantity) {
                        throw ValidationException::withMessages([
                            'stock' => [
                                "Insufficient stock for {$product->name}.",
                            ],
                        ]);
                    }

                    $order = Order::create([
                        'user_id' => $pendingCheckout['user_id'],

                        'customer_name' =>
                            $pendingCheckout['customer_name'],

                        'customer_email' =>
                            $pendingCheckout['customer_email'],

                        'product_name' =>
                            $product->name,

                        'product_image' =>
                            $product->image,

                        'quantity' =>
                            $quantity,

                        'total_price' =>
                            (float) $product->price * $quantity,

                        'address' =>
                            $pendingCheckout['address'],

                        'location' =>
                            $pendingCheckout['location'],

                        'state' =>
                            $pendingCheckout['state'],

                        'pincode' =>
                            $pendingCheckout['pincode'],

                        'status' => 'Pending',

                        'payment_status' => 'Paid',
                    ]);

                    $createdOrderIds[] = $order->id;

                    $newStock =
                        $product->stock - $quantity;

                    $product->update([
                        'stock' => $newStock,
                        'status' => $this->getProductStatus(
                            $newStock
                        ),
                    ]);
                }

                $payment = Payment::create([
                    'user_id' =>
                        $pendingCheckout['user_id'],

                    'gateway' =>
                        'razorpay',

                    'razorpay_order_id' =>
                        $validated['razorpay_order_id'],

                    'razorpay_payment_id' =>
                        $validated['razorpay_payment_id'],

                    'razorpay_signature' =>
                        $validated['razorpay_signature'],

                    'amount' =>
                        $pendingCheckout['amount'],

                    'currency' =>
                        $pendingCheckout['currency'],

                    'status' =>
                        'paid',

                    'order_ids' =>
                        $createdOrderIds,
                ]);

                return [
                    'payment' => $payment,
                    'order_ids' => $createdOrderIds,
                ];
            });

            Cache::forget($cacheKey);

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' =>
                    'Payment verified and order placed successfully.',
                'payment' => $result['payment'],
                'order_ids' => $result['order_ids'],
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'verified' => true,
                'message' =>
                    'Payment was verified, but the order could not be created. Contact support and do not pay again.',
            ], 500);
        }
    }

    private function getProductStatus(int $stock): string
    {
        if ($stock <= 0) {
            return 'sold_out';
        }

        if ($stock <= 10) {
            return 'limited_stock';
        }

        return 'in_stock';
    }
}