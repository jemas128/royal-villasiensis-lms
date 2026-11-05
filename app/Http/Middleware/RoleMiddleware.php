<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles)) {
            // Direct reroute to the user's role-specific dashboard instead of 403
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('warning', 'Access restricted to that section. Redirected to your dashboard.');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard')->with('warning', 'Access restricted to that section. Redirected to your dashboard.');
            } elseif ($user->isStudent()) {
                return redirect()->route('student.dashboard')->with('warning', 'Access restricted to that section. Redirected to your dashboard.');
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
