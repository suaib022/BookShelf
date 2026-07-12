<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityEvent;

class FeedController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $followingIds = $user->following()->pluck('users.id');
        
        $events = ActivityEvent::with(['user', 'book', 'targetUser'])
            ->whereIn('user_id', $followingIds)
            ->latest()
            ->paginate(20);
            
        return view('feed.index', compact('events'));
    }
}
