<?php

namespace App\Services;

use App\Models\User;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    public function generateForUser(User $user)
    {
        // Find genres of books rated 4+ stars
        $favoriteGenres = DB::table('ratings')
            ->join('books', 'ratings.book_id', '=', 'books.id')
            ->join('book_genre', 'books.id', '=', 'book_genre.book_id')
            ->join('genres', 'book_genre.genre_id', '=', 'genres.id')
            ->where('ratings.user_id', $user->id)
            ->where('ratings.stars', '>=', 4)
            ->select('genres.id', 'genres.name', DB::raw('COUNT(*) as count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
            
        if ($favoriteGenres->isEmpty()) {
            return;
        }
        
        $shelvedBookIds = DB::table('shelf_books')
            ->join('shelves', 'shelf_books.shelf_id', '=', 'shelves.id')
            ->where('shelves.user_id', $user->id)
            ->pluck('book_id')
            ->toArray();
            
        // Delete old recommendations
        DB::table('recommendations')->where('user_id', $user->id)->delete();
        
        $recommendations = [];
        
        foreach ($favoriteGenres as $genre) {
            $books = Book::whereHas('genres', function ($query) use ($genre) {
                    $query->where('genres.id', $genre->id);
                })
                ->whereNotIn('id', $shelvedBookIds)
                ->where('avg_rating', '>=', 3.5)
                ->where('ratings_count', '>=', 5)
                ->orderByDesc('avg_rating')
                ->limit(8)
                ->get();
                
            foreach ($books as $book) {
                if (!isset($recommendations[$book->id])) {
                    $recommendations[$book->id] = [
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'score' => $book->avg_rating,
                        'reason' => "Because you liked {$genre->name} books",
                    ];
                }
            }
        }
        
        $recommendations = collect($recommendations)->sortByDesc('score')->take(20)->values()->toArray();
        
        if (!empty($recommendations)) {
            DB::table('recommendations')->insert($recommendations);
        }
    }
}
