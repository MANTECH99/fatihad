<?php

namespace App\Http\Middleware;

use App\Services\PlanService;
use Closure;

class CheckPlanFeature
{
    public function handle($request, Closure $next, $feature)
    {
        $user = auth()->user();

        if (!PlanService::hasFeature($user, $feature)) {
            return redirect()->route('subscription.index')
                ->with('error', 'Cette fonctionnalité nécessite un plan supérieur.');
        }

        return $next($request);
    }
}
