<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shelf;

class UserProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        
        $stats = [
            'ratings' => $user->ratings()->count(),
            'avgRating' => $user->ratings()->avg('stars') ?? 0,
            'reviews' => $user->reviews()->count(),
            'followers' => $user->followers()->count(),
            'following' => $user->following()->count(),
        ];
        
        $shelves = $user->shelves()->withCount('shelfBooks')->get()->keyBy('name');
        $shelfCounts = [
            'wantToRead' => $shelves->has('Want to Read') ? $shelves->get('Want to Read')->shelf_books_count : 0,
            'currentlyReading' => $shelves->has('Currently Reading') ? $shelves->get('Currently Reading')->shelf_books_count : 0,
            'read' => $shelves->has('Read') ? $shelves->get('Read')->shelf_books_count : 0,
        ];
        
        $reviews = $user->reviews()->with('book.authors')->latest()->take(5)->get();
        $favoriteGenres = $user->favoriteGenres()->pluck('name');
        $followers = $user->followers()->take(10)->get();
        
        $isOwnProfile = auth()->check() && auth()->id() === $user->id;
        $isFollowing = auth()->check() && !$isOwnProfile && auth()->user()->following()->where('followee_id', $user->id)->exists();
        
        return view('profile.show', compact(
            'user', 'stats', 'shelfCounts', 'reviews', 'favoriteGenres', 'followers', 'isOwnProfile', 'isFollowing'
        ));
    }
}
