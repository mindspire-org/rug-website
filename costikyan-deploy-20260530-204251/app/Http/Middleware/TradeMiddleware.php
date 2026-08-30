<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TradeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, [\App\Models\User::ROLE_TRADE, \App\Models\User::ROLE_ADMIN, \App\Models\User::ROLE_TEAM])) {
            abort(403, 'Trade account required to access this area.');
        }

        return $next($request);
    }
}
