<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeamMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, [\App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_TEAM])) {
            abort(403, 'Staff account required to access this area.');
        }

        return $next($request);
    }
}
