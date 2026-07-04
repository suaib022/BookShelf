<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request, Genre $genre = null)
    {
        $query = Book::with('authors');

        // Search by title or author
        if ($search = $request->query('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhereHas('authors', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by author_id (used on book detail page)
        if ($authorId = $request->query('author_id')) {
            $query->whereHas('authors', function($q) use ($authorId) {
                $q->where('authors.id', $authorId);
            });
        }

        // Filter by genre(s)
        if ($genre) {
            $query->whereHas('genres', function($q) use ($genre) {
                $q->where('genres.id', $genre->id);
            });
        } elseif ($genreIds = $request->query('genre_id')) {
            if (is_array($genreIds) && count($genreIds) > 0) {
                $query->whereHas('genres', function($q) use ($genreIds) {
                    $q->whereIn('genres.id', $genreIds);
                });
            }
        }

        // Sort
        $sort = $request->query('sort', 'Title');
        if ($sort === 'Title') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'Newest') {
            $query->orderBy('published_date', 'desc');
        } elseif ($sort === 'Highest Rated') {
            $query->orderBy('avg_rating', 'desc');
        }

        $books = $query->paginate(12)->withQueryString();

        $allGenres = Genre::orderBy('name')->get();
        $relatedGenres = [];
        if ($genre) {
            $relatedGenres = Genre::where('id', '!=', $genre->id)->inRandomOrder()->take(5)->get();
        }

        return view('books.index', compact('books', 'genre', 'allGenres', 'relatedGenres', 'sort', 'search'));
    }

    public function show(Book $book)
    {
        $book->load(['authors', 'genres']);

        // Quick facts & Shelf counts
        $currentlyReadingCount = \DB::table('shelf_books')
            ->join('shelves', 'shelf_books.shelf_id', '=', 'shelves.id')
            ->where('shelf_books.book_id', $book->id)
            ->where('shelves.name', 'Currently Reading')
            ->count();

        $wantToReadCount = \DB::table('shelf_books')
            ->join('shelves', 'shelf_books.shelf_id', '=', 'shelves.id')
            ->where('shelf_books.book_id', $book->id)
            ->where('shelves.name', 'Want to Read')
            ->count();

        // Star breakdown
        $starBreakdown = \DB::table('ratings')
            ->select('stars', \DB::raw('count(*) as total'))
            ->where('book_id', $book->id)
            ->groupBy('stars')
            ->pluck('total', 'stars')->toArray();

        // Fill missing stars with 0
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($starBreakdown[$i])) {
                $starBreakdown[$i] = 0;
            }
        }
        krsort($starBreakdown);

        // Readers also enjoyed
        $relatedBooks = collect();
        if ($book->genres->count() > 0) {
            $relatedBooks = Book::whereHas('genres', function($q) use ($book) {
                $q->whereIn('genres.id', $book->genres->pluck('id'));
            })
            ->where('id', '!=', $book->id)
            ->orderBy('avg_rating', 'desc')
            ->take(6)
            ->get();
        }

        // Friends & Following reviews (if logged in)
        $followingReviews = collect();
        if (auth()->check()) {
            $followingIds = \DB::table('follows')
                ->where('follower_id', auth()->id())
                ->pluck('followee_id');

            if ($followingIds->count() > 0) {
                $followingReviews = \App\Models\Review::with(['user'])
                    ->where('book_id', $book->id)
                    ->whereIn('user_id', $followingIds)
                    ->latest()
                    ->get();
            }
        }

        // Community reviews
        $reviews = \App\Models\Review::with(['user'])
            ->where('book_id', $book->id)
            ->withCount(['comments', 'likes'])
            ->latest()
            ->paginate(5);

        // Logged-in user's own rating for this book
        $userRating = auth()->check()
            ? optional(\App\Models\Rating::where('user_id', auth()->id())->where('book_id', $book->id)->first())->stars
            : null;

        return view('books.show', compact(
            'book', 
            'currentlyReadingCount', 
            'wantToReadCount', 
            'starBreakdown', 
            'relatedBooks', 
            'followingReviews', 
            'reviews',
            'userRating'
        ));
    }
}
