<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    // Rejestrowanie tras
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // Rejestrowanie middleware
    ->withMiddleware(function (Middleware $middleware) {
        // Tutaj dodaj middleware, jeśli jest wymagany
    })

    // Rejestrowanie dostawców usług
    ->withProviders([
        Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class,
        // Dodaj innych dostawców usług, jeśli potrzebujesz
    ])

    // Rejestrowanie wyjątków
    ->withExceptions(function (Exceptions $exceptions) {
        // Tutaj możesz obsłużyć specyficzne wyjątki
    })

    ->create();
