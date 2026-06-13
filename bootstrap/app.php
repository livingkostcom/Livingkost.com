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
    ->withMiddleware(function (Middleware $middleware): void {
        // DOKU posts its payment notification without a CSRF token.
        $middleware->validateCsrfTokens(except: ['doku/notification']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
