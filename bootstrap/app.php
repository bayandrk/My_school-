<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Register the admin-guard middleware with an alias so it can be applied to route groups.
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        // حل مشكلة التوجيه لمنع حلقة التكرار ERR_TOO_MANY_REDIRECTS
        $middleware->redirectTo(
            guests: '/login',
            users: function () {
                if (auth()->check()) {
                    return match (auth()->user()->role) {
                        'admin'   => route('admin.dashboard'),
                        'manager' => route('manager.dashboard'),
                        'teacher' => route('teacher.dashboard'),
                        default   => route('parent.dashboard'),
                    };
                }
                return route('login');
            }
        );

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();