@extends('layouts.auth')

@section('title', 'Sign In')
@section('meta_description', 'Sign in to the Villasis State Polytechnic University LMS')

@section('content')

<div class="auth-v2-heading">
    <h1 class="auth-v2-title">Welcome back</h1>
    <p class="auth-v2-subtitle">Enter your institutional credentials to continue</p>
</div>

@if ($errors->any())
    <div class="alert alert-error" data-dismiss style="margin-bottom: 20px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success" data-dismiss style="margin-bottom: 20px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif

<form method="POST" action="{{ route('login') }}" autocomplete="off">
    @csrf

    {{-- Portal ID --}}
    <div class="auth-v2-field">
        <label for="portal_id" class="auth-v2-label">
            <span>Portal ID <span style="color: var(--error);">*</span></span>
        </label>
        <div class="auth-v2-input-wrap">
            <span class="auth-v2-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input
                type="text"
                id="portal_id"
                name="portal_id"
                value="{{ old('portal_id') }}"
                class="auth-v2-input @error('portal_id') is-invalid @enderror"
                placeholder="STU-2026-0001"
                required
                autofocus
                autocomplete="off"
                style="font-family: 'SF Mono', Consolas, Monaco, monospace; letter-spacing: 0.04em;"
            >
        </div>
        @error('portal_id')
            <div class="auth-v2-error">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Password --}}
    <div class="auth-v2-field">
        <label for="password" class="auth-v2-label">
            <span>Password <span style="color: var(--error);">*</span></span>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </label>
        <div class="auth-v2-input-wrap">
            <span class="auth-v2-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </span>
            <input
                type="password"
                id="password"
                name="password"
                class="auth-v2-input @error('password') is-invalid @enderror"
                placeholder="••••••••••••"
                required
                autocomplete="off"
            >
            <button type="button" class="auth-v2-input-toggle" onclick="togglePassword()" id="pwd-toggle" aria-label="Show password">
                <svg id="pwd-eye" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg id="pwd-eye-off" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
        </div>
        @error('password')
            <div class="auth-v2-error">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Remember me --}}
    <div class="auth-v2-checkbox-row">
        <input type="checkbox" name="remember" id="remember" class="auth-v2-checkbox" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="auth-v2-checkbox-label">Keep me signed in</label>
    </div>

    {{-- Submit --}}
    <button type="submit" class="auth-v2-btn">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        Sign In to Portal
    </button>
</form>

<div class="auth-v2-footer">
    New student or matriculating scholar?
    <a href="{{ route('register') }}">Create account</a>
    <div style="margin-top: 14px; font-size: 0.6875rem; color: var(--text-dim); text-align: center; opacity: 0.85;">
        Villasis State Polytechnic University LMS • Educational Demonstration & Coursework Project
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const eye = document.getElementById('pwd-eye');
    const eyeOff = document.getElementById('pwd-eye-off');
    if (input.type === 'password') {
        input.type = 'text';
        eye.style.display = 'none';
        eyeOff.style.display = 'block';
    } else {
        input.type = 'password';
        eye.style.display = 'block';
        eyeOff.style.display = 'none';
    }
}
</script>
@endsection
