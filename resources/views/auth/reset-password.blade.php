@extends('layouts.auth')

@section('title', 'Set New Password')
@section('meta_description', 'Set a new password for your Villasis State Polytechnic University LMS account')

@section('content')
<h1 class="auth-form-title">Set New Password</h1>
<p class="auth-form-subtitle">Create a secure password for your account</p>

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

<form method="POST" action="{{ route('password.update') }}" class="w-full">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="form-group">
        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
        <input 
            type="email" 
            id="email" 
            name="email" 
            value="{{ old('email', $email) }}" 
            class="form-input @error('email') is-invalid @enderror" 
            required 
            readonly
        >
        @error('email')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password" class="form-label">New Password <span class="required">*</span></label>
        <input 
            type="password" 
            id="password" 
            name="password" 
            class="form-input @error('password') is-invalid @enderror" 
            placeholder="Minimum 8 characters" 
            required 
            autofocus 
            autocomplete="new-password"
        >
        @error('password')
            <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirm New Password <span class="required">*</span></label>
        <input 
            type="password" 
            id="password_confirmation" 
            name="password_confirmation" 
            class="form-input" 
            placeholder="Re-type new password" 
            required 
            autocomplete="new-password"
        >
    </div>

    <button type="submit" class="btn btn-primary w-full btn-lg">
        Update Password
    </button>
</form>

<div class="auth-footer">
    <a href="{{ route('login') }}">Back to Sign In</a>
</div>
@endsection
