<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Accès non autorisé.'], 403);
            }

            return redirect()->route('login')->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
