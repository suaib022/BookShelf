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
     * Store a book on a shelf for the authenticated user.
     * If the book is already shelved, it is removed from the old shelf first.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id'    => ['required', 'integer', 'exists:books,id'],
            'shelf_name' => ['required', 'string', 'max:100'],
        ]);

        $user   = $request->user();
        $bookId = $request->input('book_id');

        // Resolve or create the target shelf for this user
        $shelf = $user->shelves()->firstOrCreate(
            ['name' => $request->input('shelf_name')],
            ['is_default' => false]
        );

        // Remove from any previous shelf first
        ShelfBook::where('user_id', $user->id)
                 ->where('book_id', $bookId)
                 ->delete();

        // Add to the target shelf
        ShelfBook::create([
            'shelf_id' => $shelf->id,
            'book_id'  => $bookId,
            'user_id'  => $user->id,
        ]);

        return back()->with('success', "Added to \"{$shelf->name}\"");
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
