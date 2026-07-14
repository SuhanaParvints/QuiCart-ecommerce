<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($query) use ($search) {
                $query
                    ->where('razorpay_payment_id', 'like', "%{$search}%")
                    ->orWhere('razorpay_order_id', 'like', "%{$search}%")
                    ->orWhere('gateway', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        return response()->json($query->get());
    }

    public function show($id)
    {
        $payment = Payment::with('user')->findOrFail($id);

        return response()->json($payment);
    }

    public function dashboard()
    {
        return response()->json([
            'total_payments' => Payment::count(),

            'paid_payments' => Payment::where('status', 'paid')->count(),

            'pending_payments' => Payment::where('status', 'pending')->count(),

            'failed_payments' => Payment::where('status', 'failed')->count(),

            'refunded_payments' => Payment::where('status', 'refunded')->count(),

            'total_revenue' => Payment::where('status', 'paid')
                ->sum('amount'),

            'today_revenue' => Payment::where('status', 'paid')
                ->whereDate('created_at', today())
                ->sum('amount'),

            'monthly_revenue' => Payment::where('status', 'paid')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ]);
    }
}