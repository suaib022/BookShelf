<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyBooksController extends Controller
{
    /**
     * Display the "My Books" (formerly My Shelves) page.
     * Sidebar shows all shelves with counts. Table supports:
     *   - shelf filter via ?shelf=
     *   - sorting via ?sort= (title|author|avg_rating|date_read|date_added)
     *   - direction via ?dir= (asc|desc)
     *   - per-page via ?per_page= (10|20|50|100)
     * Data is currently placeholder; real queries wired in a later step.
     */
    public function index(Request $request)
    {
        // ── Dummy shelf list (sidebar) ────────────────────────────────────
        $shelves = [
            ['name' => 'All',               'slug' => 'all',               'count' => 5],
            ['name' => 'Want to Read',       'slug' => 'want-to-read',       'count' => 1],
            ['name' => 'Currently Reading',  'slug' => 'currently-reading',  'count' => 2],
            ['name' => 'Read',               'slug' => 'read',               'count' => 1],
            ['name' => 'Did Not Finish',     'slug' => 'did-not-finish',     'count' => 1],
        ];

        $activeShelf = $request->input('shelf', 'all');

        // ── Dummy book rows ────────────────────────────────────────────────
        $books = [
            [
                'id'           => 1,
                'cover'        => 'https://covers.openlibrary.org/b/id/8739161-M.jpg',
                'title'        => "Rome's Age of Revolution",
                'author'       => 'Whitmarsh, Tim',
                'avg_rating'   => 4.50,
                'my_rating'    => 0,
                'shelf'        => 'did-not-finish',
                'shelf_label'  => 'did-not-finish',
                'review'       => null,
                'date_read'    => null,
                'date_added'   => '2026-07-17',
            ],
            [
                'id'           => 2,
                'cover'        => 'https://covers.openlibrary.org/b/id/10909258-M.jpg',
                'title'        => 'The God Test: Artificial Intelligence and Our Coming Cosmic Reckoning',
                'author'       => 'Wright, Robert',
                'avg_rating'   => 4.18,
                'my_rating'    => 0,
                'shelf'        => 'read',
                'shelf_label'  => 'read',
                'review'       => null,
                'date_read'    => null,
                'date_added'   => '2026-07-17',
            ],
            [
                'id'           => 3,
                'cover'        => 'https://covers.openlibrary.org/b/id/14816042-M.jpg',
                'title'        => 'DC Finest: Robin: The Origin of Robin',
                'author'       => 'Friedrich, Mike',
                'avg_rating'   => 4.00,
                'my_rating'    => 0,
                'shelf'        => 'currently-reading',
                'shelf_label'  => 'currently-reading',
                'review'       => null,
                'date_read'    => null,
                'date_added'   => '2026-07-17',
            ],
            [
                'id'           => 4,
                'cover'        => 'https://covers.openlibrary.org/b/id/14635072-M.jpg',
                'title'        => 'Empire of AI: Dreams and Nightmares in Sam Altman\'s OpenAI',
                'author'       => 'Hao, Karen',
                'avg_rating'   => 4.02,
                'my_rating'    => 0,
                'shelf'        => 'currently-reading',
                'shelf_label'  => 'currently-reading',
                'review'       => null,
                'date_read'    => null,
                'date_added'   => '2026-07-17',
            ],
            [
                'id'           => 5,
                'cover'        => 'https://covers.openlibrary.org/b/id/14888823-M.jpg',
                'title'        => "Breakneck: China's Quest to Engineer the Future",
                'author'       => 'Wang, Dan',
                'avg_rating'   => 4.09,
                'my_rating'    => 0,
                'shelf'        => 'want-to-read',
                'shelf_label'  => 'to-read',
                'review'       => null,
                'date_read'    => null,
                'date_added'   => '2026-07-17',
            ],
        ];

        // Filter by active shelf
        if ($activeShelf !== 'all') {
            $books = array_filter($books, fn($b) => $b['shelf'] === $activeShelf);
        }

        // Sorting
        $sort = $request->input('sort', 'date_added');
        $dir  = $request->input('dir', 'desc');
        usort($books, function ($a, $b) use ($sort, $dir) {
            $valA = $a[$sort] ?? '';
            $valB = $b[$sort] ?? '';
            $cmp  = strcmp((string) $valA, (string) $valB);
            return $dir === 'asc' ? $cmp : -$cmp;
        });

        // Per page (just for display — no real pagination with dummy data)
        $perPage = (int) $request->input('per_page', 20);

        return view('my-books.index', compact('shelves', 'books', 'activeShelf', 'sort', 'dir', 'perPage'));
    }
}
