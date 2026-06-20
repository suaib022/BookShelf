<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityEvent;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            $shelves = $user->shelves()->whereIn('name', ['Read', 'Currently Reading', 'Want to Read'])->get()->keyBy('name');
            
            $currentlyReadingShelf = $shelves->get('Currently Reading');
            $wantToReadShelf = $shelves->get('Want to Read');
            $readShelf = $shelves->get('Read');
            
            $currentlyReadingBooks = $currentlyReadingShelf ? $currentlyReadingShelf->shelfBooks()->with('book.authors')->get() : collect();
            $wantToReadBooks = $wantToReadShelf ? $wantToReadShelf->shelfBooks()->with('book.authors')->get() : collect();
            
            $shelfCounts = [
                'Read' => $readShelf ? $readShelf->shelfBooks()->count() : 0,
                'Currently Reading' => $currentlyReadingBooks->count(),
                'Want to Read' => $wantToReadBooks->count(),
            ];
            
            $followingIds = $user->following()->pluck('users.id');
            
            $activityEvents = ActivityEvent::with(['user', 'book', 'targetUser'])
                ->whereIn('user_id', $followingIds)
                ->latest()
                ->paginate(15);
                
            return view('home', compact('currentlyReadingBooks', 'wantToReadBooks', 'shelfCounts', 'activityEvents'));
        }
        
        return view('home');
    }
}
