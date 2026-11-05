<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailVerificationCodeMail;
use App\Mail\RegistrationReceivedMail;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        // Generate security token for checkbox captcha
        $captchaChallenge = Str::random(32);
        session(['captcha_challenge' => $captchaChallenge]);

        return view('auth.register', compact('departments', 'captchaChallenge'));
    }

    public function verifyCheckboxCaptcha(Request $request): JsonResponse
    {
        $challenge = $request->input('challenge');
        $sessionChallenge = session('captcha_challenge');

        if ($challenge && $sessionChallenge && hash_equals($sessionChallenge, $challenge)) {
            $verificationToken = hash_hmac('sha256', $challenge . session()->getId(), config('app.key'));
            session(['captcha_verified_token' => $verificationToken]);

            return response()->json([
                'success' => true,
                'token'   => $verificationToken,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Verification failed'], 422);
    }

    public function register(Request $request): RedirectResponse
    {
        // Verify Google-style checkbox captcha token
        $submittedToken = $request->input('captcha_token');
        $validToken     = session('captcha_verified_token');

        if (!$validToken || !hash_equals($validToken, (string) $submittedToken)) {
            return back()->withInput()->withErrors([
                'captcha' => 'Please confirm that you are not a robot by checking the security box.',
            ]);
        }

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'department_id' => ['nullable', 'exists:departments,id'],
        ]);

        // Generate 6-digit confirmation code
        $code = (string) random_int(100000, 999999);

        // Store pending registration data in session
        session([
            'pending_registration' => [
                'name'          => $request->name,
                'email'         => $request->email,
                'department_id' => $request->department_id,
                'code'          => $code,
                'created_at'    => now()->timestamp,
            ],
        ]);

        // Send verification code via Laravel SMTP
        try {
            Mail::to($request->email)
                ->send(new EmailVerificationCodeMail($code, $request->name));
        } catch (\Exception $e) {
            Log::warning("Could not send verification code to {$request->email}: " . $e->getMessage());
        }

        $message = "A 6-digit verification code has been dispatched to {$request->email}. Please check your inbox or spam folder.";
        if (app()->environment('local')) {
            $message .= " [LOCAL: Your code is {$code}]";
        }

        return redirect()->route('register.verify')->with('status', $message);
    }

    public function showVerifyForm(): View|RedirectResponse
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->withErrors([
                'email' => 'No registration session found. Please fill out the registration form.',
            ]);
        }

        return view('auth.verify-code', [
            'email' => $pending['email'],
            'name'  => $pending['name'],
        ]);
    }

    public function confirmVerification(Request $request): RedirectResponse
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register')->withErrors([
                'email' => 'Registration session has expired. Please register again.',
            ]);
        }

        $request->validate([
            'code' => ['required', 'string', 'min:6', 'max:10'],
        ]);

        $submittedCode = trim($request->input('code'));

        if ($submittedCode !== (string) $pending['code']) {
            return back()->withErrors([
                'code' => 'The entered confirmation code is incorrect. Please double-check your email.',
            ]);
        }

        // Code matches — create the user
        $studentId       = 'STU-' . date('Y') . '-' . str_pad(User::count() + 1, 4, '0', STR_PAD_LEFT);
        $numericPassword = (string) random_int(10000000, 99999999);

        $user = User::create([
            'name'                      => $pending['name'],
            'email'                     => $pending['email'],
            'password'                  => Hash::make($numericPassword),
            'initial_password_encrypted'=> Crypt::encryptString($numericPassword),
            'role'                      => 'student',
            'student_id'                => $studentId,
            'department_id'             => $pending['department_id'],
            'phone'                     => null,
            'is_active'                 => false, // Pending administrative clearance
        ]);

        // Send registration received acknowledgment via Laravel SMTP
        try {
            Mail::to($user->email)
                ->send(new RegistrationReceivedMail($user));
        } catch (\Exception $e) {
            Log::warning("Could not send registration receipt to {$user->email}: " . $e->getMessage());
        }

        // Clean up session
        session()->forget(['pending_registration', 'captcha_verified_token', 'captcha_challenge']);

        return redirect()->route('login')->with(
            'status',
            "Your email has been verified and your account is officially submitted! You will receive your portal login confirmation via email once approved by University Administration."
        );
    }

    public function resendCode(): RedirectResponse
    {
        $pending = session('pending_registration');
        if (!$pending) {
            return redirect()->route('register');
        }

        $newCode           = (string) random_int(100000, 999999);
        $pending['code']   = $newCode;
        session(['pending_registration' => $pending]);

        // Resend verification code via Laravel SMTP
        try {
            Mail::to($pending['email'])
                ->send(new EmailVerificationCodeMail($newCode, $pending['name']));
        } catch (\Exception $e) {
            Log::warning("Could not resend verification code to {$pending['email']}: " . $e->getMessage());
        }

        $message = 'A new 6-digit confirmation code has been dispatched to your email.';
        if (app()->environment('local')) {
            $message .= " [LOCAL: Your code is {$newCode}]";
        }

        return back()->with('status', $message);
    }
}
