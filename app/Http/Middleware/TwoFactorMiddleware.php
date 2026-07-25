<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->google2fa_enabled && !session('2fa_verified')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
