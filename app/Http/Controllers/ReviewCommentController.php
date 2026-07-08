<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\ReviewComment;

class ReviewCommentController extends Controller
{
    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request, Review $review)
    {
        $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $review->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->input('body'),
        ]);

        return back()->with('success', 'Comment posted.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(ReviewComment $comment)
    {
        abort_if($comment->user_id !== request()->user()->id, 403);

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}
