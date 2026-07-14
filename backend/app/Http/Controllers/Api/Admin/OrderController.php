<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Orders
    |--------------------------------------------------------------------------
    */

    public function myOrders(Request $request)
    {
        return response()->json(
            Order::where('user_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => [
                'required',
                'string',
                'max:255',
            ],

            'product_image' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'total_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'address' => [
                'required',
                'string',
                'max:1000',
            ],

            'location' => [
                'required',
                'string',
                'max:255',
            ],

            'state' => [
                'required',
                'string',
                'max:255',
            ],

            'pincode' => [
                'required',
                'string',
                'max:20',
            ],

            'payment_method' => [
                'nullable',
                'string',
                'max:50',
            ],

            'payment_status' => [
                'nullable',
                'string',
                'max:50',
            ],
        ]);

        $order = Order::create([
            'user_id' => $request->user()->id,
            'customer_name' => $request->user()->name,
            'customer_email' => $request->user()->email,

            'product_name' => $validated['product_name'],
            'product_image' => $validated['product_image'] ?? null,
            'quantity' => $validated['quantity'],
            'total_price' => $validated['total_price'],

            'address' => $validated['address'],
            'location' => $validated['location'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],

            'status' => 'Pending',

            'payment_status' =>
                $validated['payment_status'] ??
                'Cash On Delivery',
        ]);

        return response()->json([
            'message' => 'Order placed successfully.',
            'order' => $order,
        ], 201);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($order->status === 'Delivered') {
            return response()->json([
                'message' =>
                    'Delivered orders cannot be cancelled.',
            ], 422);
        }

        if ($order->status === 'Cancelled') {
            return response()->json([
                'message' =>
                    'This order is already cancelled.',
                'order' => $order,
            ]);
        }

        $order->update([
            'status' => 'Cancelled',
        ]);

        return response()->json([
            'message' => 'Order cancelled successfully.',
            'order' => $order->fresh(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        $paidPayments = Payment::where('status', 'paid');

        return response()->json([
            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */

            'total_users' => User::count(),

            /*
            |--------------------------------------------------------------------------
            | Orders
            |--------------------------------------------------------------------------
            */

            'total_orders' => Order::count(),

            'pending_orders' => Order::where(
                'status',
                'Pending'
            )->count(),

            'processing_orders' => Order::where(
                'status',
                'Processing'
            )->count(),

            'shipped_orders' => Order::where(
                'status',
                'Shipped'
            )->count(),

            'completed_orders' => Order::where(
                'status',
                'Delivered'
            )->count(),

            'delivered_orders' => Order::where(
                'status',
                'Delivered'
            )->count(),

            'cancelled_orders' => Order::where(
                'status',
                'Cancelled'
            )->count(),

            /*
            |--------------------------------------------------------------------------
            | Products
            |--------------------------------------------------------------------------
            */

            'total_products' => Product::count(),

            'in_stock_products' => Product::where(
                'status',
                'in_stock'
            )->count(),

            'limited_stock_products' => Product::where(
                'status',
                'limited_stock'
            )->count(),

            'sold_out_products' => Product::where(
                'status',
                'sold_out'
            )->count(),

            /*
            |--------------------------------------------------------------------------
            | Reviews
            |--------------------------------------------------------------------------
            */

            'total_reviews' => Review::count(),

            'approved_reviews' => Review::where(
                'status',
                'approved'
            )->count(),

            'pending_reviews' => Review::where(
                'status',
                'pending'
            )->count(),

            'rejected_reviews' => Review::where(
                'status',
                'rejected'
            )->count(),

            'average_rating' => round(
                (float) (Review::avg('rating') ?? 0),
                1
            ),

            /*
            |--------------------------------------------------------------------------
            | Payment and Revenue
            |--------------------------------------------------------------------------
            |
            | Revenue is calculated from successful Razorpay payments in the
            | payments table. It is not calculated from order status.
            |
            */

            'total_payments' => Payment::count(),

            'paid_payments' => Payment::where(
                'status',
                'paid'
            )->count(),

            'pending_payments' => Payment::where(
                'status',
                'pending'
            )->count(),

            'failed_payments' => Payment::where(
                'status',
                'failed'
            )->count(),

            'refunded_payments' => Payment::where(
                'status',
                'refunded'
            )->count(),

            'total_revenue' => (float) (
                clone $paidPayments
            )->sum('amount'),

            'today_revenue' => (float) Payment::where(
                'status',
                'paid'
            )
                ->whereDate('created_at', today())
                ->sum('amount'),

            'monthly_revenue' => (float) Payment::where(
                'status',
                'paid'
            )
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),

            'yearly_revenue' => (float) Payment::where(
                'status',
                'paid'
            )
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Orders
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return response()->json(
            Order::latest()->get()
        );
    }

    public function show($id)
    {
        return response()->json(
            Order::findOrFail($id)
        );
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'Pending',
                    'Processing',
                    'Shipped',
                    'Delivered',
                    'Cancelled',
                ]),
            ],
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'message' => 'Order status updated successfully.',
            'order' => $order->fresh(),
        ]);
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully.',
        ]);
    }
}