@extends('layouts.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span class="current">Profile</span>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto;">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 style="font-size: var(--text-2xl); color: var(--ivory); font-family: var(--font-serif); letter-spacing: -0.01em; margin-bottom: 4px;">Account Profile</h1>
        <p style="font-size: var(--text-sm); color: var(--text-muted);">Manage your identity and security settings</p>
    </div>

    {{-- Identity Overview --}}
    <div style="background: linear-gradient(135deg, rgba(200,164,93,0.06) 0%, rgba(26,26,26,0.9) 100%); border: 1px solid rgba(200,164,93,0.2); border-radius: var(--radius-xl); padding: var(--space-6); margin-bottom: var(--space-6); display: flex; align-items: center; gap: var(--space-5);">
        {{-- Avatar --}}
        <div style="position: relative; flex-shrink: 0;">
            <div style="width: 72px; height: 72px; border-radius: 50%; background: linear-gradient(135deg, rgba(200,164,93,0.2), rgba(200,164,93,0.05)); border: 1.5px solid var(--gold-border); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 1.75rem; font-weight: 700; color: var(--gold); font-family: var(--font-serif); line-height: 1;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
            </div>
            <div style="position: absolute; bottom: 2px; right: 2px; width: 12px; height: 12px; background: var(--success); border-radius: 50%; border: 2px solid #111;"></div>
        </div>

        {{-- Identity Info --}}
        <div style="flex: 1; min-width: 0;">
            <h2 style="font-size: var(--text-xl); color: var(--ivory); font-weight: 600; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $user->name }}</h2>
            <div style="display: flex; gap: var(--space-2); align-items: center; flex-wrap: wrap;">
                <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(200,164,93,0.1); color: var(--gold); border: 1px solid rgba(200,164,93,0.25); padding: 2px 10px; border-radius: 999px; font-size: 0.7rem; text-transform: uppercase; font-weight: 600; letter-spacing: 0.06em;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ ucfirst($user->role) }}
                </span>

                @if($user->student_id)
                    <span style="font-size: var(--text-xs); color: var(--text-secondary); font-family: monospace; background: rgba(255,255,255,0.04); padding: 2px 8px; border-radius: var(--radius-sm); border: 1px solid var(--border-dark);">{{ $user->student_id }}</span>
                @elseif($user->employee_id)
                    <span style="font-size: var(--text-xs); color: var(--text-secondary); font-family: monospace; background: rgba(255,255,255,0.04); padding: 2px 8px; border-radius: var(--radius-sm); border: 1px solid var(--border-dark);">{{ $user->employee_id }}</span>
                @endif

                @if($user->department)
                    <span style="font-size: var(--text-xs); color: var(--text-muted); display: flex; align-items: center; gap: 4px;">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                        {{ $user->department->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Email display --}}
        <div style="flex-shrink: 0; text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
            <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Email</div>
            <div style="font-size: var(--text-sm); color: var(--text-secondary);">{{ $user->email }}</div>
        </div>
    </div>

    {{-- Two-column forms --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: var(--space-5);">

        {{-- Personal Information --}}
        <div style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="padding: var(--space-4) var(--space-6); border-bottom: 1px solid var(--border-dark); display: flex; align-items: center; gap: var(--space-3);">
                <div style="width: 30px; height: 30px; border-radius: var(--radius-md); background: rgba(200,164,93,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div style="font-size: var(--text-sm); color: var(--ivory); font-weight: 600;">Personal Information</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);">Update your name and contact details</div>
                </div>
            </div>

            <div style="padding: var(--space-5) var(--space-6);">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name" class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input @error('name') is-invalid @enderror" required>
                        @error('name')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: var(--space-5);">
                        <label for="phone" class="form-label">Contact Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input @error('phone') is-invalid @enderror" placeholder="+63 900 000 0000">
                        @error('phone')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; gap: 8px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Save Changes
                    </button>
                </form>
            </div>
        </div>

        {{-- Security / Password --}}
        <div style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="padding: var(--space-4) var(--space-6); border-bottom: 1px solid var(--border-dark); display: flex; align-items: center; gap: var(--space-3);">
                <div style="width: 30px; height: 30px; border-radius: var(--radius-md); background: rgba(91,127,158,0.1); display: flex; align-items: center; justify-content: center;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--info)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                </div>
                <div>
                    <div style="font-size: var(--text-sm); color: var(--ivory); font-weight: 600;">Security & Password</div>
                    <div style="font-size: 0.7rem; color: var(--text-muted);">Change your account password</div>
                </div>
            </div>

            <div style="padding: var(--space-5) var(--space-6);">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="current_password" class="form-label">Current Password <span class="required">*</span></label>
                        <input type="password" id="current_password" name="current_password" class="form-input @error('current_password') is-invalid @enderror" required placeholder="Your current password">
                        @error('current_password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">New Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" class="form-input @error('password') is-invalid @enderror" required placeholder="Minimum 8 characters">
                        @error('password')
                            <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: var(--space-5);">
                        <label for="password_confirmation" class="form-label">Confirm New Password <span class="required">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-input" required placeholder="Re-type new password">
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center; gap: 8px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
