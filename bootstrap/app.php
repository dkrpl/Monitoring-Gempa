<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LogUserActivity;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware untuk WEB routes (dengan session)
        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class, // CSRF hanya untuk web
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            LogUserActivity::class,
        ]);

        // Middleware untuk API routes (TANPA session dan CSRF)
        $middleware->api(prepend: [
            // Tidak ada session middleware di sini!
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Nonaktifkan CSRF untuk API routes
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/v1/*',
            'sanctum/csrf-cookie',
        ]);

        // Global middleware
        $middleware->prepend(\App\Http\Middleware\RedirectBasedOnRole::class);

        // Atau untuk spesifik ke web saja
        // $middleware->appendToGroup('web', LogUserActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
