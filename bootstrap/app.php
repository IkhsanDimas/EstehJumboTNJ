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
        $middleware->redirectTo(guests: '/admin/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Tangkap error asli dan tampilkan sebagai teks biasa di layar browser
        $exceptions->render(function (\Throwable $e) {
            header('Content-Type: text/plain');
            echo "RAW EXCEPTION:\n" . $e->getMessage() . "\n\n";
            echo "FILE: " . $e->getFile() . " (Line " . $e->getLine() . ")\n\n";
            echo "TRACE:\n" . $e->getTraceAsString();
            exit;
        });
    })->create();
