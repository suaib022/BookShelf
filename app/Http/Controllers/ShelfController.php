<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelf;
use App\Models\ShelfBook;

class ShelfController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Add/move a book to a shelf.
     * A user's book can only live on ONE shelf at a time.
     * Only one default shelf (Want to Read / Currently Reading / Read / Did Not Finish)
     * can be active per book — book is removed from all other shelves before placement.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'    => ['required', 'integer', 'exists:books,id'],
            'shelf_name' => ['required', 'string', 'max:100'],
        ]);

        $user      = $request->user();
        $bookId    = (int) $request->input('book_id');
        $shelfName = $request->input('shelf_name');

        // Resolve or create the shelf for this user
        $shelf = $user->shelves()->firstOrCreate(
            ['name' => $shelfName],
            ['is_default' => false]
        );

        // Only one default shelf at a time:
        // Remove the book from ALL of this user's shelves before placing it on the new one.
        $userShelfIds = $user->shelves()->pluck('id')->toArray();

        ShelfBook::where('user_id', $user->id)
                 ->where('book_id', $bookId)
                 ->whereIn('shelf_id', $userShelfIds)
                 ->delete();

        // Place the book on the target shelf
        ShelfBook::create([
            'shelf_id' => $shelf->id,
            'book_id'  => $bookId,
            'user_id'  => $user->id,
        ]);

        return back()->with('success', "Moved to \"{$shelf->name}\"");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
