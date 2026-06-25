<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ModeToggleController extends Controller
{
    public function toggle(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            abort(403, 'Only admins can toggle modes.');
        }

        // Toggle the mode
        if ($user->active_mode === 'admin') {
            $user->update(['active_mode' => 'user']);
            
            if (str_contains(url()->previous(), '/admin')) {
                return redirect()->route('profile.show', $user->username)->with('success', 'Switched to User Mode.');
            }

            return back()->with('success', 'Switched to User Mode.');
        } else {
            $user->update(['active_mode' => 'admin']);
            return back()->with('success', 'Switched to Admin Mode.');
        }
    }
}
