<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function productReviews($productId)
    {
        return Review::where('product_id', $productId)
            ->where('status', 'approved')
            ->latest()
            ->get();
    }

    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'experience' => 'required|in:good,average,bad',
            'comment' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $product = Product::findOrFail($productId);
        $user = $request->user();

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        $review = Review::create([
            'user_id' => $user?->id,
            'product_id' => $product->id,
            'customer_name' => $user?->name ?? 'Guest User',
            'customer_email' => $user?->email ?? 'guest@example.com',
            'product_name' => $product->name,
            'rating' => $request->rating,
            'experience' => $request->experience,
            'comment' => $request->comment,
            'image' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Review submitted successfully. Waiting for admin approval.',
            'review' => $review,
        ], 201);
    }
}