<?php

namespace App\Services;

use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function generateForUser(User $user)
    {
        $recommendations = [];

        // 1. Books already on shelves (to exclude)
        $shelvedBookIds = DB::table('shelf_books')
            ->join('shelves', 'shelf_books.shelf_id', '=', 'shelves.id')
            ->where('shelves.user_id', $user->id)
            ->pluck('book_id')
            ->toArray();

        // Helper to add/update score
        $addCandidate = function($book, $scoreBonus, $reason) use (&$recommendations, $shelvedBookIds) {
            if (in_array($book->id, $shelvedBookIds)) return;
            
            if (!isset($recommendations[$book->id])) {
                $recommendations[$book->id] = [
                    'user_id' => null, // Set later
                    'book_id' => $book->id,
                    'score' => $book->avg_rating, // Base score is the average rating
                    'reason' => $reason,
                ];
            }
            $recommendations[$book->id]['score'] += $scoreBonus;
        };

        // --- SOURCE 1: Favorite Genres ---
        $favoriteGenres = $user->favoriteGenres;
        foreach ($favoriteGenres as $genre) {
            $books = Book::whereHas('genres', function ($query) use ($genre) {
                    $query->where('genres.id', $genre->id);
                })
                ->where('avg_rating', '>=', 3.5)
                ->where('ratings_count', '>=', 1) // Lowered count threshold for seeding sparsity
                ->orderByDesc('avg_rating')
                ->limit(10)
                ->get();
                
            foreach ($books as $book) {
                $addCandidate($book, 2.0, "Because you like {$genre->name}");
            }
        }

        // --- SOURCE 2: People Following ---
        $followedUsers = $user->following()->pluck('users.id')->toArray();
        if (!empty($followedUsers)) {
            $followedHighlyRated = DB::table('ratings')
                ->whereIn('user_id', $followedUsers)
                ->where('stars', '>=', 4)
                ->select('book_id', 'user_id', 'stars')
                ->orderByDesc('created_at')
                ->limit(20)
                ->get();

            foreach ($followedHighlyRated as $rating) {
                $book = Book::find($rating->book_id);
                if ($book) {
                    $followedUser = User::find($rating->user_id);
                    // Append reason carefully if it doesn't exist
                    $addCandidate($book, 3.0, "Because {$followedUser->name} rated this {$rating->stars} stars");
                }
            }
        }

        // --- SOURCE 3: Personal Reading History (Highly rated genres) ---
        $highlyRatedGenres = DB::table('ratings')
            ->join('book_genre', 'ratings.book_id', '=', 'book_genre.book_id')
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
            ->where('ratings.user_id', $user->id)
            ->where('ratings.stars', '>=', 4)
            ->select('genres.id', 'genres.name', DB::raw('COUNT(*) as count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->limit(3)
            ->get();
            
        foreach ($highlyRatedGenres as $genre) {
            $books = Book::whereHas('genres', function ($query) use ($genre) {
                    $query->where('genres.id', $genre->id);
                })
                ->where('avg_rating', '>=', 4.0)
                ->orderByDesc('avg_rating')
                ->limit(5)
                ->get();
                
            foreach ($books as $book) {
                $addCandidate($book, 1.5, "Because you rated {$genre->name} books highly");
            }
        }

        // --- SOURCE 4: Fallback (Global Trending/Popular) ---
        if (count($recommendations) < 5) {
            $popularBooks = Book::where('avg_rating', '>=', 4.0)
                ->where('ratings_count', '>=', 1)
                ->orderByDesc('ratings_count')
                ->orderByDesc('avg_rating')
                ->limit(10)
                ->get();

            foreach ($popularBooks as $book) {
                $addCandidate($book, 0.5, "Trending on Bookshelf");
            }
        }

        // Clean and persist
        DB::table('recommendations')->where('user_id', $user->id)->delete();
        
        $finalRecommendations = collect($recommendations)
            ->sortByDesc('score')
            ->take(20)
            ->map(function ($rec) use ($user) {
                $rec['user_id'] = $user->id;
                return $rec;
            })
            ->values()
            ->toArray();
            
        if (!empty($finalRecommendations)) {
            DB::table('recommendations')->insert($finalRecommendations);
        }
    }
}
