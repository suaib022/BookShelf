<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\Genre;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $genres = Genre::all();
        $userGenres = $request->user()->favoriteGenres()->pluck('genres.id')->toArray();
        
        return view('profile.edit', [
            'user' => $request->user(),
            'genres' => $genres,
            'userGenres' => $userGenres,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());
        
        $user->username = $request->input('username');
        $user->location = $request->input('location');
        $user->website = $request->input('website');
        $user->bio = $request->input('bio');
        $user->is_private = $request->boolean('is_private');

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        
        if ($request->hasFile('avatar')) {
            $request->validate([
                'avatar' => ['image', 'max:2048']
            ]);
            
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = $path;
        }

        $user->save();
        
        if ($request->has('genres')) {
            $user->favoriteGenres()->sync($request->input('genres', []));
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        
        if ($user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
