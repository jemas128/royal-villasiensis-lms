@extends('layouts.app')

@section('title', 'Provision New User Account')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <span class="separator">/</span>
    <span class="current">New Account</span>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    
    {{-- Header --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); margin-bottom: var(--space-6); flex-wrap: wrap;">
        <div>
            <h1 class="page-title">Provision User Account</h1>
            <p class="page-subtitle">Register a new scholar, faculty instructor, or administrator with auto-generated credentials</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Directory
        </a>
    </div>

    {{-- Automated Credentials Feature Banner --}}
    <div class="credentials-banner-card" style="margin-bottom: var(--space-5);">
        <div style="display: flex; align-items: flex-start; gap: var(--space-4);">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(200, 164, 93, 0.15); border: 1px solid var(--gold); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--gold-light);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </div>
            <div>
                <h3 style="font-size: 1.05rem; color: var(--ivory); font-weight: 600; margin-bottom: 4px;">
                    Automated Portal ID &amp; Password Provisioning
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 0;">
                    When created, the system generates a unique <strong>Institutional Portal ID</strong> (e.g. <code style="color: var(--gold-light);">FAC-2026-0004</code> or <code style="color: var(--gold-light);">STU-2026-0001</code>) and an encrypted <strong>temporary password</strong>. Credentials will be displayed on screen immediately and dispatched to their registered email.
                </p>
            </div>
        </div>
    </div>

    {{-- Main Create Form --}}
    <form method="POST" action="{{ route('admin.users.store') }}" autocomplete="off">
        @csrf

        {{-- Section 1: Legal Identity & Institutional Contact --}}
        <div class="user-edit-section">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Personal &amp; Contact Details
            </div>
            <div class="user-edit-section-desc">
                Name and institutional contact information for notification dispatch
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Full Legal Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input @error('name') is-invalid @enderror" placeholder="e.g. Prof. Marcus Aurelius" required autofocus>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Institutional / Contact Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-input @error('email') is-invalid @enderror" placeholder="e.g. instructor@university.edu" required>
                    <span class="form-hint">Portal credentials will be sent to this email</span>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="phone" class="form-label">Phone Contact Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-input" placeholder="e.g. +63 912 345 6789" autocomplete="tel">
                <span class="form-hint">Optional contact number for directory</span>
            </div>
        </div>

        {{-- Section 2: Institutional Role & Academic Department --}}
        <div class="user-edit-section">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Role &amp; Academic Placement
            </div>
            <div class="user-edit-section-desc">
                Determine portal access privileges and department affiliation
            </div>

            <div class="form-row" style="margin-bottom: 0;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="role" class="form-label">Institutional Role <span class="required">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                        <option value="teacher" {{ old('role', 'teacher') === 'teacher' ? 'selected' : '' }}>Faculty / Teacher</option>
                        <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student / Scholar</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                    @error('role')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="department_id" class="form-label">College / Department</label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror">
                        <option value="">None / General University</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} — {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 3: Activation Status --}}
        <div class="user-edit-section" style="background: rgba(26, 26, 26, 0.7);">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Account Governance
            </div>
            <div class="user-edit-section-desc">
                Account status upon creation
            </div>

            <div>
                <label class="form-toggle-switch">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="form-toggle-slider"></span>
                    <div>
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--ivory);">Activate Account Immediately</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">Enable instant login using generated credentials without requiring pending approval</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center mt-6" style="padding-top: var(--space-4);">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Provision Account &amp; Generate Credentials
            </button>
        </div>
    </form>
</div>
@endsection
