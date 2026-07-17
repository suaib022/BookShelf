<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShelfBook;
use Illuminate\Support\Str;

class MyBooksController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ── Shelf list (sidebar) ────────────────────────────────────
        $dbShelves = $user->shelves()->withCount('shelfBooks')->get();
        $allCount = ShelfBook::where('user_id', $user->id)->count();

        $defaultNames = ['Want to Read', 'Currently Reading', 'Read', 'Did Not Finish'];
        $shelvesMap = [];
        
        foreach ($defaultNames as $name) {
            $shelvesMap[$name] = [
                'id' => null,
                'name' => $name,
                'slug' => Str::slug($name),
                'count' => 0,
                'is_custom' => false
            ];
        }

        foreach ($dbShelves as $ds) {
            $isDefault = in_array($ds->name, $defaultNames);
            $shelvesMap[$ds->name] = [
                'id' => $ds->id,
                'name' => $ds->name,
                'slug' => Str::slug($ds->name),
                'count' => $ds->shelf_books_count,
                'is_custom' => !$isDefault
            ];
        }

        $shelves = [
            ['id' => null, 'name' => 'All', 'slug' => 'all', 'count' => $allCount, 'is_custom' => false],
        ];

        foreach ($shelvesMap as $s) {
            $shelves[] = $s;
        }

        $activeShelf = $request->input('shelf', 'all');

        // ── Book rows ────────────────────────────────────────────────
        $query = ShelfBook::select('shelf_books.*')
            ->where('shelf_books.user_id', $user->id)
            ->join('books', 'shelf_books.book_id', '=', 'books.id')
            ->with([
                'book.authors',
                'shelf',
                'book.reviews' => function($q) use ($user) {
                    $q->where('user_id', $user->id);
                },
                'book.ratings' => function($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ]);

        if ($activeShelf !== 'all') {
            $shelfMatch = collect($shelves)->first(fn($s) => $s['slug'] === $activeShelf);
            if ($shelfMatch && $shelfMatch['id']) {
                $query->where('shelf_books.shelf_id', $shelfMatch['id']);
            } else {
                $query->where('shelf_books.shelf_id', 0); // Not found
            }
        }

        // Sorting
        $sort = $request->input('sort', 'date_added');
        $dir  = $request->input('dir', 'desc');

        if ($sort === 'title') {
            $query->orderBy('books.title', $dir);
        } elseif ($sort === 'author') {
            // Simplified sorting for author: join first author
            $query->leftJoin('book_author', function ($join) {
                $join->on('books.id', '=', 'book_author.book_id')
                     ->whereRaw('book_author.id = (select min(id) from book_author where book_id = books.id)');
            })
            ->leftJoin('authors', 'book_author.author_id', '=', 'authors.id')
            ->orderBy('authors.name', $dir);
        } elseif ($sort === 'avg_rating') {
            $query->orderBy('books.avg_rating', $dir);
        } elseif ($sort === 'date_read') {
            $query->orderBy('shelf_books.date_finished', $dir);
        } else {
            // default: date_added
            $query->orderBy('shelf_books.created_at', $dir);
        }

        $perPage = (int) $request->input('per_page', 20);
        $perPage = in_array($perPage, [10, 20, 50, 100]) ? $perPage : 20;

        $paginator = $query->paginate($perPage)->withQueryString();

        // Transform for view
        $books = $paginator->getCollection()->map(function ($sb) {
            $b = $sb->book;
            $rating = $b->ratings->first();
            $review = $b->reviews->first();
            return [
                'id'           => $b->id,
                'cover'        => $b->cover_url ? (filter_var($b->cover_url, FILTER_VALIDATE_URL) ? $b->cover_url : \Storage::url($b->cover_url)) : null,
                'title'        => $b->title,
                'author'       => $b->authors->pluck('name')->join(', '),
                'avg_rating'   => $b->avg_rating,
                'my_rating'    => $rating ? $rating->stars : 0,
                'shelf'        => $sb->shelf ? $sb->shelf->name : '',
                'shelf_label'  => $sb->shelf ? $sb->shelf->name : '',
                'review'       => $review ? $review->body : null,
                'review_id'    => $review ? $review->id : null,
                'date_read'    => $sb->date_finished ? \Carbon\Carbon::parse($sb->date_finished)->format('M j, Y') : null,
                'date_added'   => $sb->created_at,
            ];
        });

        $paginator->setCollection($books);

        return view('my-books.index', compact('shelves', 'paginator', 'activeShelf', 'sort', 'dir', 'perPage'));
    }
}
