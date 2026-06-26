<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Author;
use App\Models\Genre;
use App\Services\ImgbbService;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with('authors')->latest()->paginate(15);
        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authors = Author::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        return view('admin.books.create', compact('authors', 'genres'));
    }

    /**
     * Store a newly created resource in storage.
     */
    private function processTags(Request $request, $key, $modelClass)
    {
        $items = $request->input($key, []);
        $processed = [];
        foreach ($items as $item) {
            if (str_starts_with($item, 'NEW::')) {
                $name = substr($item, 5);
                $new = $modelClass::firstOrCreate(['name' => $name]);
                $processed[] = $new->id;
            } else {
                $processed[] = $item;
            }
        }
        $request->merge([$key => $processed]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ImgbbService $imgbbService)
    {
        $this->processTags($request, 'authors', Author::class);
        $this->processTags($request, 'genres', Genre::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'isbn10' => 'nullable|string|max:10',
            'isbn13' => 'nullable|string|max:13',
            'page_count' => 'nullable|integer|min:1',
            'published_date' => 'nullable|date',
            'language' => 'required|string|max:50',
            'cover_image_file' => 'nullable|image|max:2048',
            'cover_url' => 'nullable|url|max:255', // If they use Google Books API image
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $coverUrl = $request->input('cover_url');

        if ($request->hasFile('cover_image_file')) {
            try {
                $coverUrl = $imgbbService->uploadImage($request->file('cover_image_file'));
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['cover_image_file' => 'Failed to upload cover: ' . $e->getMessage()]);
            }
        }

        if (!$coverUrl) {
            return back()->withInput()->withErrors(['cover_image_file' => 'A cover image file or URL is required.']);
        }

        $book = Book::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'description' => $validated['description'],
            'isbn10' => $validated['isbn10'],
            'isbn13' => $validated['isbn13'],
            'page_count' => $validated['page_count'],
            'published_date' => $validated['published_date'],
            'language' => $validated['language'],
            'cover_url' => $coverUrl,
            'added_by_admin_id' => auth()->id(),
        ]);

        $book->authors()->sync($validated['authors']);
        $book->genres()->sync($validated['genres']);

        return redirect()->route('admin.books.index')->with('success', 'Book saved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();
        $genres = Genre::orderBy('name')->get();
        $bookAuthors = $book->authors->pluck('id')->toArray();
        $bookGenres = $book->genres->pluck('id')->toArray();

        return view('admin.books.edit', compact('book', 'authors', 'genres', 'bookAuthors', 'bookGenres'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book, ImgbbService $imgbbService)
    {
        $this->processTags($request, 'authors', Author::class);
        $this->processTags($request, 'genres', Genre::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'required|string',
            'isbn10' => 'nullable|string|max:10',
            'isbn13' => 'nullable|string|max:13',
            'page_count' => 'nullable|integer|min:1',
            'published_date' => 'nullable|date',
            'language' => 'required|string|max:50',
            'cover_image_file' => 'nullable|image|max:2048',
            'cover_url' => 'nullable|url|max:255',
            'authors' => 'required|array',
            'authors.*' => 'exists:authors,id',
            'genres' => 'required|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $coverUrl = $request->input('cover_url', $book->cover_url);

        if ($request->hasFile('cover_image_file')) {
            try {
                $coverUrl = $imgbbService->uploadImage($request->file('cover_image_file'));
            } catch (\Exception $e) {
                return back()->withInput()->withErrors(['cover_image_file' => 'Failed to upload cover: ' . $e->getMessage()]);
            }
        }

        $book->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'],
            'description' => $validated['description'],
            'isbn10' => $validated['isbn10'],
            'isbn13' => $validated['isbn13'],
            'page_count' => $validated['page_count'],
            'published_date' => $validated['published_date'],
            'language' => $validated['language'],
            'cover_url' => $coverUrl,
        ]);

        $book->authors()->sync($validated['authors']);
        $book->genres()->sync($validated['genres']);

        return redirect()->route('admin.books.index')->with('success', 'Book updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Book deleted successfully.');
    }

    /**
     * Search Google Books API.
     */
    public function searchGoogleBooks(Request $request)
    {
        $query = $request->query('q');
        
        if (!$query) {
            return response()->json(['items' => []]);
        }

        try {
            $params = [
                'q' => $query,
                'maxResults' => 10,
            ];
            
            if (env('GOOGLE_BOOKS_API_KEY')) {
                $params['key'] = env('GOOGLE_BOOKS_API_KEY');
            }

            $response = Http::get('https://www.googleapis.com/books/v1/volumes', $params);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Google Books API error', 
                'details' => $response->body()
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
