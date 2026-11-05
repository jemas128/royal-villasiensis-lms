@extends('layouts.auth')

@section('title', 'Register')
@section('meta_description', 'Create a new Villasis State Polytechnic University LMS account')

@section('content')

<div class="auth-v2-heading">
    <h1 class="auth-v2-title">Create account</h1>
    <p class="auth-v2-subtitle">Register for university academic portal access</p>
</div>

@if (session('status'))
    <div class="alert alert-success" data-dismiss style="margin-bottom: 20px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
        <span>{{ session('status') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-error" data-dismiss style="margin-bottom: 20px;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span>{{ $errors->first() }}</span>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    {{-- Full Name --}}
    <div class="auth-v2-field">
        <label for="name" class="auth-v2-label">Full Name <span style="color:var(--error);">*</span></label>
        <div class="auth-v2-input-wrap">
            <span class="auth-v2-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                class="auth-v2-input @error('name') is-invalid @enderror"
                placeholder="e.g. Eleanor Vance"
                required
                autofocus
                autocomplete="name"
            >
        </div>
        @error('name')
            <div class="auth-v2-error">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Institutional Email --}}
    <div class="auth-v2-field">
        <label for="email" class="auth-v2-label">Institutional Email <span style="color:var(--error);">*</span></label>
        <div class="auth-v2-input-wrap">
            <span class="auth-v2-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </span>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                class="auth-v2-input @error('email') is-invalid @enderror"
                placeholder="e.g. e.vance@rvu.edu"
                required
                autocomplete="email"
            >
        </div>
        @error('email')
            <div class="auth-v2-error">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $message }}
            </div>
        @enderror
    </div>

    {{-- Academic Department --}}
    <div class="auth-v2-field">
        <label for="department_id" class="auth-v2-label">Academic Department</label>
        <div class="auth-v2-input-wrap">
            <span class="auth-v2-input-icon">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
            </span>
            <select name="department_id" id="department_id" class="auth-v2-select @error('department_id') is-invalid @enderror">
                <option value="">Select College / Department…</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->code }} — {{ $dept->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('department_id')
            <div class="auth-v2-error">{{ $message }}</div>
        @enderror
    </div>

    {{-- Divider --}}
    <div class="auth-v2-divider"><span>Security Verification</span></div>

    {{-- reCAPTCHA-style Box --}}
    <div style="margin-bottom: 22px;">
        <div id="recaptcha-box" style="height: 64px; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; cursor: pointer; user-select: none; transition: border-color 0.2s ease, box-shadow 0.2s ease;">
            <div style="display: flex; align-items: center; gap: 13px;">
                <div id="recaptcha-anchor" style="width: 24px; height: 24px; background: rgba(255,255,255,0.05); border: 1.5px solid rgba(255,255,255,0.15); border-radius: 5px; display: flex; align-items: center; justify-content: center; transition: all 0.2s ease; flex-shrink: 0;">
                    <svg id="recaptcha-check" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    <div id="recaptcha-spinner" style="display: none; width: 14px; height: 14px; border: 2px solid rgba(200, 164, 93, 0.2); border-top-color: var(--gold); border-radius: 50%; animation: rvuSpin 0.7s linear infinite;"></div>
                </div>
                <span id="recaptcha-label" style="font-size: 0.875rem; color: var(--text-secondary); font-weight: 500;">I'm not a robot</span>
            </div>
            <div style="display: flex; flex-direction: column; align-items: center; gap: 2px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="opacity: 0.75;">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M9 12l2 2 4-4"/>
                </svg>
                <div style="font-size: 7.5px; font-weight: 700; letter-spacing: 0.06em; color: var(--text-dim); text-transform: uppercase;">reCAPTCHA</div>
                <div style="font-size: 7px; color: var(--text-dim); opacity: 0.6;">Privacy · Terms</div>
            </div>
        </div>
        <input type="hidden" name="captcha_token" id="captcha_token" value="">
        <div id="recaptcha-error" style="display: none; margin-top: 6px; font-size: 0.72rem; color: var(--error); display: none;">
            Please confirm you're not a robot.
        </div>
        @error('captcha')
            <div class="auth-v2-error" style="margin-top: 6px;">{{ $message }}</div>
        @enderror
    </div>

    <style>
        @keyframes rvuSpin { to { transform: rotate(360deg); } }
    </style>

    {{-- Submit --}}
    <button type="submit" id="submit-btn" class="auth-v2-btn">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Continue to Email Verification
    </button>
</form>

<script>
    const recaptchaBox = document.getElementById('recaptcha-box');
    const recaptchaAnchor = document.getElementById('recaptcha-anchor');
    const recaptchaCheck = document.getElementById('recaptcha-check');
    const recaptchaSpinner = document.getElementById('recaptcha-spinner');
    const recaptchaToken = document.getElementById('captcha_token');
    const recaptchaError = document.getElementById('recaptcha-error');
    const registerForm = recaptchaBox ? recaptchaBox.closest('form') : null;
    let isRobotVerified = false;

    recaptchaBox?.addEventListener('click', function() {
        if (isRobotVerified) return;
        recaptchaAnchor.style.borderColor = 'var(--gold)';
        recaptchaSpinner.style.display = 'block';
        if (recaptchaError) recaptchaError.style.display = 'none';

        fetch('{{ route('captcha.verify') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ challenge: '{{ $captchaChallenge }}' })
        })
        .then(res => res.json())
        .then(data => {
            setTimeout(() => {
                recaptchaSpinner.style.display = 'none';
                if (data.success && data.token) {
                    recaptchaCheck.style.display = 'block';
                    recaptchaAnchor.style.borderColor = '#22C55E';
                    recaptchaAnchor.style.background = 'rgba(34,197,94,0.15)';
                    recaptchaBox.style.borderColor = 'rgba(34,197,94,0.4)';
                    recaptchaToken.value = data.token;
                    isRobotVerified = true;
                    document.getElementById('recaptcha-label').textContent = 'Verification complete';
                    document.getElementById('recaptcha-label').style.color = '#22C55E';
                } else {
                    recaptchaAnchor.style.borderColor = '#EF4444';
                }
            }, 550);
        })
        .catch(() => {
            recaptchaSpinner.style.display = 'none';
            recaptchaAnchor.style.borderColor = '#EF4444';
        });
    });

    registerForm?.addEventListener('submit', function(e) {
        if (!recaptchaToken.value) {
            e.preventDefault();
            recaptchaBox.style.borderColor = 'rgba(184,92,92,0.6)';
            recaptchaBox.style.boxShadow = '0 0 0 3px rgba(184,92,92,0.1)';
            if (recaptchaError) recaptchaError.style.display = 'block';
            recaptchaBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>

<div class="auth-v2-footer">
    Already have an account?
    <a href="{{ route('login') }}">Sign in</a>
</div>
@endsection
