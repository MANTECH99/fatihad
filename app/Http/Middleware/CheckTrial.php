<?php

namespace App\Http\Middleware;

use Closure;

class CheckTrial
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Vérifier l'abonnement actif
        $subscription = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $blocked = false;

        if ($subscription) {
            // Plan gratuit : bloqué si trial expiré ou pas de trial (ancien payant)
            if ($subscription->plan === 'free') {
                if (!$subscription->trial_ends_at || $subscription->trial_ends_at->isPast()) {
                    $blocked = true;
                }
            }
            // Plan payant : bloqué si ends_at dépassé
            if ($subscription->plan !== 'free' && $subscription->ends_at && $subscription->ends_at->isPast()) {
                $subscription->update(['status' => 'expired']);
                $user->update(['plan' => 'free', 'trial_ends_at' => null]);
                $blocked = true;
            }
        } else {
            // Pas d'abonnement = bloqué
            $blocked = true;
        }

        if ($blocked && !$request->routeIs('subscription.*') && !$request->routeIs('logout')) {
            return redirect()->route('subscription.index')
                ->with('error', 'Votre abonnement a expiré. Choisissez un plan pour continuer.');
        }

        return $next($request);
    }
}
