<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'portal_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');
        $portalId = $request->input('portal_id');

        // Check student_id, employee_id, or institutional email
        $user = \App\Models\User::where('student_id', $portalId)
            ->orWhere('employee_id', $portalId)
            ->orWhere('email', $portalId)
            ->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->password], $remember)) {
            $request->session()->regenerate();

            if (! $user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'portal_id' => 'Your account is pending administrative verification and approval. Please await confirmation from the University Administration.',
                ]);
            }

            // Validate any intended URL to ensure it matches the user's role privileges
            $intended = $request->session()->pull('url.intended');
            if ($intended) {
                $path = parse_url($intended, PHP_URL_PATH) ?? '';
                $basePath = $request->getBasePath();
                if ($basePath && str_starts_with($path, $basePath)) {
                    $path = substr($path, strlen($basePath));
                }

                $isAllowed = false;
                if ($user->isAdmin() && !str_starts_with($path, '/teacher') && !str_starts_with($path, '/student')) {
                    $isAllowed = true;
                } elseif ($user->isTeacher() && str_starts_with($path, '/teacher')) {
                    $isAllowed = true;
                } elseif ($user->isStudent() && str_starts_with($path, '/student')) {
                    $isAllowed = true;
                }

                if (str_starts_with($path, '/profile') || str_starts_with($path, '/announcements')) {
                    $isAllowed = true;
                }

                if ($isAllowed) {
                    return redirect()->to($intended);
                }
            }

            // Direct reroute to role-specific dashboard
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isTeacher()) {
                return redirect()->route('teacher.dashboard');
            } else {
                return redirect()->route('student.dashboard');
            }
        }

        return back()->withErrors([
            'portal_id' => 'The provided credentials do not match our records.',
        ])->onlyInput('portal_id');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been successfully signed out.');
    }
}
