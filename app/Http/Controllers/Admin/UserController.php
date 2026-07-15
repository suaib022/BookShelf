<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($search = $request->input('q')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $activities = collect();

        // Get recent reviews
        $reviews = $user->reviews()->with('book')->latest()->take(10)->get()->map(function ($review) {
            return [
                'type' => 'review',
                'title' => 'Reviewed: ' . $review->book->title,
                'content' => $review->body,
                'date' => $review->created_at,
                'url' => route('books.show', $review->book)
            ];
        });

        // Get recent shelf additions
        $shelfBooks = $user->books()->withPivot('status', 'created_at')->orderByPivot('created_at', 'desc')->take(10)->get()->map(function ($book) {
            return [
                'type' => 'shelf',
                'title' => 'Shelved: ' . $book->title,
                'content' => 'Status: ' . ucfirst($book->pivot->status),
                'date' => $book->pivot->created_at,
                'url' => route('books.show', $book)
            ];
        });

        $activities = $reviews->concat($shelfBooks)->sortByDesc('date')->take(15);

        return view('admin.users.show', compact('user', 'activities'));
    }
    public function ban(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        $user->update(['is_banned' => true]);
        return back()->with('success', 'User has been banned successfully.');
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);
        return back()->with('success', 'User has been unbanned successfully.');
    }
}
