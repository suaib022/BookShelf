<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Rating;
use App\Models\Book;

class ReviewController extends Controller
{
    /**
     * Store a new review for the authenticated user.
     * Links to their existing rating automatically if one exists.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'           => ['required', 'integer', 'exists:books,id'],
            'body'              => ['required', 'string', 'min:10'],
            'contains_spoilers' => ['boolean'],
        ]);

        $user   = $request->user();
        $bookId = (int) $request->input('book_id');

        // Link to existing rating if the user has one
        $rating = Rating::where('user_id', $user->id)
                        ->where('book_id', $bookId)
                        ->first();

        // Prevent duplicate review — update if already exists
        Review::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $bookId],
            [
                'body'              => $request->input('body'),
                'contains_spoilers' => $request->boolean('contains_spoilers'),
                'rating_id'         => $rating?->id,
            ]
        );

        return back()->with('success', 'Your review has been saved.');
    }

    /**
     * Update the authenticated user's review.
     * Only the review's author can edit it.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'body'              => ['required', 'string', 'min:10'],
            'contains_spoilers' => ['boolean'],
        ]);

        $review = Review::findOrFail($id);

        // Ownership check
        abort_if($review->user_id !== $request->user()->id, 403);

        $review->update([
            'body'              => $request->input('body'),
            'contains_spoilers' => $request->boolean('contains_spoilers'),
        ]);

        return back()->with('success', 'Review updated.');
    }

    /**
     * Delete a review. Only the review's author can delete it.
     */
    public function destroy(string $id)
    {
        $review = Review::findOrFail($id);

        abort_if($review->user_id !== request()->user()->id, 403);

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }

    public function index() {}
    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
}
