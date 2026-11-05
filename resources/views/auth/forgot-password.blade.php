@extends('layouts.auth')

@section('title', 'Forgot Password')
@section('meta_description', 'Recover your Villasis State Polytechnic University LMS account')

@section('content')
<h1 class="auth-form-title">Reset Password</h1>
<p class="auth-form-subtitle">Enter your institutional email to receive a password reset link</p>

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

<form method="POST" action="{{ route('password.email') }}" class="w-full">
    @csrf

    <div class="form-group">
        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email') }}" 
            class="form-input @error('email') is-invalid @enderror" 
            placeholder="e.g. scholar@rvu.edu" 
            required 
            autofocus
        >
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary w-full btn-lg">
        Send Password Reset Link
    </button>
</form>

<div class="auth-footer">
    Remembered your credentials? 
    <a href="{{ route('login') }}">Return to Sign In</a>
</div>
@endsection
