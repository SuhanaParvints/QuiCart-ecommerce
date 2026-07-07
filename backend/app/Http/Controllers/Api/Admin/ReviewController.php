<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function index()
    {
        return Review::latest()->get();
    }

    public function dashboard()
    {
        return response()->json([
            'total_reviews' => Review::count(),
            'pending_reviews' => Review::where('status', 'pending')->count(),
            'approved_reviews' => Review::where('status', 'approved')->count(),
            'rejected_reviews' => Review::where('status', 'rejected')->count(),
            'average_rating' => round(Review::avg('rating'), 1),
            'good_reviews' => Review::where('experience', 'good')->count(),
            'average_reviews' => Review::where('experience', 'average')->count(),
            'bad_reviews' => Review::where('experience', 'bad')->count(),
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review = Review::findOrFail($id);

        $review->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Review status updated',
            'review' => $review,
        ]);
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);

        if ($review->image) {
            Storage::disk('public')->delete($review->image);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted',
        ]);
    }
}