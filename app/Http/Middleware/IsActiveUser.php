<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActiveUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->isActiveUser()) {
            // They are logged in but in admin mode, trying to access a user route.
            return redirect()->route('admin.dashboard')->with('error', 'Please switch to User Mode to access this feature.');
        }

        return $next($request);
    }
}
