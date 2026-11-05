@extends('layouts.auth')

@section('title', 'Verify Institutional Email')
@section('meta_description', 'Enter the 6-digit verification code sent to your email')

@section('content')
<div class="text-center mb-6">
    <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(200, 164, 93, 0.12); border: 1px solid rgba(200, 164, 93, 0.4); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--gold);">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
        </svg>
    </div>
    <h1 class="auth-form-title" style="font-size: 1.6rem; margin-bottom: 6px;">Email Confirmation</h1>
    <p class="auth-form-subtitle" style="font-size: 0.875rem; margin-bottom: 0;">
        We have dispatched a 6-digit verification code to:
    </p>
    <div style="color: var(--gold); font-weight: 600; font-size: 0.95rem; margin-top: 4px;">
        {{ $email }}
    </div>
</div>

@if (session('status'))
    <div class="alert alert-success mb-4" data-dismiss>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error mb-4" data-dismiss>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('register.verify') }}" class="w-full">
    @csrf

    <div class="form-group mb-6">
        <label for="code" class="form-label text-center" style="display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 8px;">
            Enter 6-Digit Confirmation Code
        </label>
        <div style="max-width: 260px; margin: 0 auto;">
            <input 
                type="text" 
                id="code" 
                name="code" 
                value="{{ old('code') }}" 
                class="form-input text-center @error('code') is-invalid @enderror" 
                placeholder="••••••" 
                maxlength="6"
                pattern="[0-9]{6}"
                inputmode="numeric"
                required 
                autofocus 
                autocomplete="one-time-code"
                style="font-family: monospace; font-size: 1.8rem; letter-spacing: 0.35em; font-weight: 700; padding: 10px 14px; text-align: center; color: var(--gold); border-color: rgba(200, 164, 93, 0.4);"
            >
        </div>
        @error('code')
            <div class="form-error text-center" style="margin-top: 8px;">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-full btn-lg mb-4">
        Verify Code & Submit Application
    </button>
</form>

<div class="flex justify-between items-center pt-2" style="border-top: 1px solid var(--border-dark); font-size: 0.825rem;">
    <form method="POST" action="{{ route('register.resend') }}">
        @csrf
        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--gold); padding: 4px 8px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            Resend Code
        </button>
    </form>

    <a href="{{ route('register') }}" style="color: var(--text-dim); text-decoration: none;">
        Change Email
    </a>
</div>
@endsection
