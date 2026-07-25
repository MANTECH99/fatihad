<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'plan.feature' => \App\Http\Middleware\CheckPlanFeature::class,
            'check.trial' => \App\Http\Middleware\CheckTrial::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'wave/callback',
            'payment/callback',
            'payment/callback/*',
            'admin/cashout/callback',
            'subscription/callback/*',  // ← Ajouter
            'subscription/callback/*/*',  // ← AJOUTER
            'certification/callback/*',
            'certification/callback/*/*',
            'certification/callback*', // <-- AJOUTE CECI
            'certification/callback', // <-- AJOUTE CECI (pour l'URL racine)
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
