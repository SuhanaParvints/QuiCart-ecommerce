<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Customer Orders
    |--------------------------------------------------------------------------
    */

    public function myOrders(Request $request)
    {
        return Order::where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

  public function store(Request $request)
{
    $request->validate([
        'product_name' => 'required',
        'product_image' => 'nullable|string',
        'quantity' => 'required|integer',
        'total_price' => 'required|numeric',
        'address' => 'required',
        'location' => 'required',
        'state' => 'required',
        'pincode' => 'required',
    ]);

    $order = Order::create([
        'user_id' => $request->user()->id,
        'customer_name' => $request->user()->name,
        'customer_email' => $request->user()->email,
        'product_name' => $request->product_name,
        'product_image' => $request->product_image,
        'quantity' => $request->quantity,
        'total_price' => $request->total_price,
        'address' => $request->address,
        'location' => $request->location,
        'state' => $request->state,
        'pincode' => $request->pincode,
        'status' => 'Pending',
        'payment_status' => 'Cash On Delivery',
    ]);

    return response()->json([
        'message' => 'Order placed successfully',
        'order' => $order,
    ]);
    }

    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->status === 'Delivered') {
            return response()->json([
                'message' => 'Delivered orders cannot be cancelled'
            ], 422);
        }

        $order->update([
            'status' => 'Cancelled'
        ]);

        return response()->json([
            'message' => 'Order cancelled'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin Dashboard
    |--------------------------------------------------------------------------
    */

public function dashboard()
{
    return response()->json([
        'total_users' => User::count(),

        'total_orders' => Order::count(),
        'pending_orders' => Order::where('status', 'Pending')->count(),
        'shipped_orders' => Order::where('status', 'Shipped')->count(),
        'completed_orders' => Order::where('status', 'Delivered')->count(),
        'cancelled_orders' => Order::where('status', 'Cancelled')->count(),

        'total_products' => Product::count(),
        'in_stock_products' => Product::where('status', 'in_stock')->count(),
        'limited_stock_products' => Product::where('status', 'limited_stock')->count(),
        'sold_out_products' => Product::where('status', 'sold_out')->count(),

        'total_reviews' => Review::count(),
        'approved_reviews' => Review::where('status', 'approved')->count(),
        'pending_reviews' => Review::where('status', 'pending')->count(),
        'rejected_reviews' => Review::where('status', 'rejected')->count(),
        'average_rating' => round(Review::avg('rating') ?? 0, 1),

        'total_revenue' => Order::where('status', 'Delivered')->sum('total_price'),
    ]);
}
    /*
    |--------------------------------------------------------------------------
    | Admin Orders
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        return Order::latest()->get();
    }

    public function show($id)
    {
        return Order::findOrFail($id);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Order status updated',
            'order' => $order
        ]);
    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Order deleted'
        ]);
    }
}