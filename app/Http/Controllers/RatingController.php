<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityEvent;
use App\Models\Book;
use App\Models\Rating;

class RatingController extends Controller
{
    /**
     * Upsert a rating for the authenticated user.
     * One rating per user per book — update if it already exists, create if not.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => ['required', 'integer', 'exists:books,id'],
            'stars'   => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $user   = $request->user();
        $bookId = (int) $request->input('book_id');
        $stars  = (int) $request->input('stars');

        $rating = Rating::updateOrCreate(
            ['user_id' => $user->id, 'book_id' => $bookId],
            ['stars'   => $stars]
        );

        // Recalculate book aggregate stats
        $book = Book::findOrFail($bookId);
        $book->updateAvgRating();

        if ($rating->wasRecentlyCreated || $rating->wasChanged('stars')) {
            ActivityEvent::create([
                'user_id' => $user->id,
                'type'    => 'rate',
                'book_id' => $bookId,
                'metadata' => ['stars' => $stars],
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'avg_rating'    => round($book->fresh()->avg_rating, 2),
                'ratings_count' => $book->fresh()->ratings_count,
                'user_rating'   => $stars,
            ]);
        }

        return back()->with('success', "You rated this book {$stars} star" . ($stars > 1 ? 's' : '') . '.');
    }

    /**
     * Delete the authenticated user's rating for a book.
     */
    public function destroy(string $id)
    {
        $user   = request()->user();
        $rating = Rating::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        $bookId = $rating->book_id;

        $rating->delete();

        // Recalculate book aggregate stats
        $book = Book::findOrFail($bookId);
        $book->updateAvgRating();

        if (request()->expectsJson()) {
            return response()->json([
                'avg_rating'    => round($book->fresh()->avg_rating, 2),
                'ratings_count' => $book->fresh()->ratings_count,
                'user_rating'   => 0,
            ]);
        }

        return back()->with('success', 'Your rating has been removed.');
    }

    public function index() {}
    public function create() {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
}
