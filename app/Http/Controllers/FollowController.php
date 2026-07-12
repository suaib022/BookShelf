<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityEvent;
use App\Models\User;
class FollowController extends Controller
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

    public function store(Request $request, User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 422);
        }

        $result = auth()->user()->following()->syncWithoutDetaching([$user->id]);

        if (in_array($user->id, $result['attached'] ?? [])) {
            ActivityEvent::create([
                'user_id' => auth()->id(),
                'type'    => 'follow',
                'target_user_id' => $user->id,
            ]);
        }

        return response()->json([
            'status' => 'following',
            'followers_count' => $user->followers()->count()
        ]);
    }

    public function destroy(User $user)
    {
        auth()->user()->following()->detach($user->id);

        return response()->json([
            'status' => 'unfollowed',
            'followers_count' => $user->followers()->count()
        ]);
    }
}
