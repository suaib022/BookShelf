<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewLike;

class ReviewLikeController extends Controller
{
    /**
     * Toggle a like on a review.
     */
    public function toggle(Request $request, Review $review)
    {
        $user = $request->user();
        
        $like = ReviewLike::where('user_id', $user->id)
                          ->where('review_id', $review->id)
                          ->first();

        if ($like) {
            $like->delete();
            $status = 'unliked';
        } else {
            ReviewLike::create([
                'user_id' => $user->id,
                'review_id' => $review->id,
            ]);
            $status = 'liked';
        }

        // Update the count on the review model (optional but good for performance)
        $count = ReviewLike::where('review_id', $review->id)->count();
        $review->update(['likes_count' => $count]);

        return response()->json([
            'status' => $status,
            'likes_count' => $count
        ]);
    }
}
