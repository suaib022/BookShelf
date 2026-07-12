<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shelf;
use App\Models\ShelfBook;
use App\Models\ActivityEvent;

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
     * Create a brand-new custom shelf (no book attached).
     * Used from the "Add shelf" button on the My Books page.
     */
    public function create()
    {
        //
    }

    /**
     * Add/move a book to a shelf (or create a custom shelf and shelve the book there).
     * Only one shelf per user per book is allowed at a time — book is removed from all
     * other shelves (default or custom) before being placed on the target shelf.
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

        // Only one shelf at a time: remove from all existing shelves first
        $userShelfIds = $user->shelves()->pluck('id')->toArray();

        ShelfBook::where('user_id', $user->id)
                 ->where('book_id', $bookId)
                 ->whereIn('shelf_id', $userShelfIds)
                 ->delete();

        ShelfBook::create([
            'shelf_id' => $shelf->id,
            'book_id'  => $bookId,
            'user_id'  => $user->id,
        ]);

        ActivityEvent::create([
            'user_id' => $user->id,
            'type'    => 'shelve',
            'book_id' => $bookId,
            'metadata' => ['shelf' => $shelfName],
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
     * Rename a custom shelf.
     * Default shelves (is_default = true) cannot be renamed.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['name' => ['required', 'string', 'max:100']]);

        $shelf = Shelf::where('id', $id)
                      ->where('user_id', $request->user()->id)
                      ->firstOrFail();

        if ($shelf->is_default) {
            return back()->withErrors(['name' => 'Default shelves cannot be renamed.']);
        }

        $shelf->update(['name' => $request->input('name')]);

        return back()->with('success', 'Shelf renamed.');
    }

    /**
     * Delete a custom shelf and all its ShelfBook entries.
     * Default shelves (is_default = true) are protected from deletion.
     */
    public function destroy(string $id)
    {
        $user  = request()->user();
        $shelf = Shelf::where('id', $id)
                      ->where('user_id', $user->id)
                      ->firstOrFail();

        if ($shelf->is_default) {
            return back()->withErrors(['shelf' => 'Default shelves cannot be deleted.']);
        }

        ShelfBook::where('shelf_id', $shelf->id)->delete();
        $shelf->delete();

        return back()->with('success', "Shelf \"{$shelf->name}\" deleted.");
    }
}
