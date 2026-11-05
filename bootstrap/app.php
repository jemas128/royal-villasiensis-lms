<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust OpenResty / reverse proxy headers for optimal performance on shared hosting
        $middleware->trustProxies(at: '*');

        // Exclude logout from CSRF verification to prevent 419 Page Expired errors on sign out
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle 403 Access Denied globally: reroute authenticated users to their role dashboard, guests to login
        $exceptions->render(function (HttpException $e, Request $request) {
            try {
                if ($e->getStatusCode() === 403) {
                    if (! Auth::check()) {
                        return redirect()->route('login');
                    }

                    $user = $request->user();
                    if ($user?->isAdmin()) {
                        return redirect()->route('admin.dashboard')->with('warning', 'Access restricted. Redirected to your dashboard.');
                    } elseif ($user?->isTeacher()) {
                        return redirect()->route('teacher.dashboard')->with('warning', 'Access restricted. Redirected to your dashboard.');
                    } elseif ($user?->isStudent()) {
                        return redirect()->route('student.dashboard')->with('warning', 'Access restricted. Redirected to your dashboard.');
                    }

                    return redirect()->route('dashboard');
                }

                if ($e->getStatusCode() === 419) {
                    return redirect()->route('login')->with('warning', 'Your session has expired. Please sign in again.');
                }
            } catch (\Throwable $t) {
                return null;
            }

            return null;
        });

        // Handle expired CSRF tokens / session expiry smoothly by redirecting to login
        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return redirect()->route('login')->with('warning', 'Your session has expired. Please sign in again.');
        });
    })->create();

