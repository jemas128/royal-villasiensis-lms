@extends('layouts.app')

@section('title', 'Edit User — ' . $user->name)

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.users.index') }}">Users</a>
    <span class="separator">/</span>
    <span class="current">Edit {{ $user->name }}</span>
@endsection

@section('content')
<div style="max-width: 860px; margin: 0 auto;">
    
    {{-- Header Profile Badge --}}
    <div style="display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); margin-bottom: var(--space-6); flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #2D271D 0%, #161514 100%); border: 2px solid var(--gold); color: var(--gold-light); display: flex; align-items: center; justify-content: center; font-family: var(--font-serif); font-size: 1.35rem; font-weight: 700; box-shadow: 0 4px 14px rgba(200, 164, 93, 0.25);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <h1 class="page-title" style="margin-bottom: 0;">{{ $user->name }}</h1>
                    @if($user->role === 'admin')
                        <span class="badge badge-gold">Administrator</span>
                    @elseif($user->role === 'teacher')
                        <span class="badge badge-success">Faculty / Teacher</span>
                    @else
                        <span class="badge badge-info">Student / Scholar</span>
                    @endif

                    @if($user->is_active)
                        <span class="badge" style="background: rgba(91, 140, 90, 0.15); color: #78C277; border: 1px solid rgba(91, 140, 90, 0.3);">
                            ● Active
                        </span>
                    @else
                        <span class="badge" style="background: rgba(196, 151, 59, 0.15); color: #E5B24D; border: 1px solid rgba(196, 151, 59, 0.3);">
                            ○ Suspended / Pending
                        </span>
                    @endif
                </div>
                <p class="page-subtitle" style="margin-top: 4px;">
                    {{ $user->email }} &bull; Member since {{ $user->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>

        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Directory
        </a>
    </div>

    {{-- Official Institutional Credentials & Portal Access Card --}}
    <div class="credentials-banner-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: var(--space-4); flex-wrap: wrap; margin-bottom: var(--space-5);">
            <div>
                <div style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.12em; color: var(--gold); font-weight: 700; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Portal Access &amp; Credentials
                </div>
                <h3 style="font-size: 1.15rem; color: var(--ivory); font-weight: 600; margin-bottom: 2px;">
                    Institutional Authentication Identity
                </h3>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">
                    Provisioned clearance credentials for portal authentication and instructor communications.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.users.resend-credentials', $user) }}">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm" title="Dispatch clearance email to {{ $user->email }}">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    Resend Email Credentials
                </button>
            </form>
        </div>

        {{-- Stacked Credential Dossier Rows (Prevents Any UI Button Collision) --}}
        <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: var(--space-4);">
            
            {{-- Item 1: Institutional Portal ID --}}
            <div class="credential-item-row">
                <div class="cred-left">
                    <div class="cred-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="cred-meta">
                        <div class="cred-label">
                            {{ $user->role === 'student' ? 'Student Portal ID' : 'Faculty / Employee ID' }}
                        </div>
                        <div class="cred-value">
                            <span id="display-portal-id" style="font-family: 'SF Mono', Consolas, Monaco, monospace; font-size: 1.1rem; font-weight: 700; color: var(--gold-light); letter-spacing: 0.04em;">
                                {{ $user->portal_id }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="cred-actions">
                    <button type="button" class="copy-pill-btn" onclick="copyText('display-portal-id', this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <span>Copy ID</span>
                    </button>
                </div>
            </div>

            {{-- Item 2: Temporary / Initial Password --}}
            <div class="credential-item-row">
                <div class="cred-left">
                    <div class="cred-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div class="cred-meta">
                        <div class="cred-label">
                            Temporary / Initial Password
                        </div>
                        <div class="cred-value">
                            @if($user->initial_password)
                                <span id="display-password" data-plain="{{ $user->initial_password }}" style="font-family: 'SF Mono', Consolas, Monaco, monospace; font-size: 1.05rem; font-weight: 700; color: var(--ivory); letter-spacing: 0.06em; background: rgba(255,255,255,0.06); padding: 2px 8px; border-radius: 4px;">
                                    ••••••••••••
                                </span>
                            @else
                                <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
                                    Custom / Managed by user
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="cred-actions">
                    @if($user->initial_password)
                        <button type="button" class="copy-pill-btn" id="btn-toggle-pw" onclick="togglePasswordVisibility()" title="Show/Hide Password">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="icon-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span id="text-toggle-pw">Reveal</span>
                        </button>
                        <button type="button" class="copy-pill-btn" onclick="copyRawPassword('{{ $user->initial_password }}', this)">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                            <span>Copy Password</span>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Item 3: Institutional Email --}}
            <div class="credential-item-row">
                <div class="cred-left">
                    <div class="cred-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <div class="cred-meta">
                        <div class="cred-label">
                            Official Institutional Email
                        </div>
                        <div class="cred-value">
                            <span id="display-email" style="font-size: 0.95rem; font-weight: 600; color: var(--ivory);">
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="cred-actions">
                    <button type="button" class="copy-pill-btn" onclick="copyText('display-email', this)">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                        <span>Copy Email</span>
                    </button>
                </div>
            </div>

        </div>

        {{-- Official Institutional Dispatch Notice --}}
        <div style="font-size: 0.775rem; color: var(--text-muted); line-height: 1.5; display: flex; align-items: flex-start; gap: 10px; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: var(--radius-md); padding: 10px 14px;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" style="flex-shrink: 0; margin-top: 2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <div>
                <strong style="color: var(--gold-light);">Institutional Dispatch:</strong>
                Clearance credentials are sent directly to the instructor's registered institutional email (<code style="color: var(--ivory); font-weight: 600;">{{ $user->email }}</code>). You can also click the copy buttons above to share the portal ID and temporary password directly with the user.
            </div>
        </div>
    </div>

    {{-- Main Edit Account Form --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}" autocomplete="off">
        @csrf
        @method('PUT')

        {{-- Section 1: Legal Identity & Institutional Contact --}}
        <div class="user-edit-section">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Personal &amp; Contact Details
            </div>
            <div class="user-edit-section-desc">
                Legal identification and institutional communications channel
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label">Full Legal Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="form-input @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Institutional Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="form-input @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row" style="margin-bottom: 0;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="phone" class="form-label">Phone Contact Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="e.g. +63 912 345 6789" autocomplete="tel">
                    <span class="form-hint">Mobile or institutional extension</span>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label for="department_id" class="form-label">College / Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">None / General University</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $user->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} — {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section 2: Institutional Role & Portal Identification --}}
        <div class="user-edit-section">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                Role &amp; Institutional Identifiers
            </div>
            <div class="user-edit-section-desc">
                Access tier permissions and official University ID numbers
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="role" class="form-label">Institutional Role <span class="required">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required onchange="handleRoleChange(this.value)">
                        <option value="student" {{ old('role', $user->role) === 'student' ? 'selected' : '' }}>Student / Scholar</option>
                        <option value="teacher" {{ old('role', $user->role) === 'teacher' ? 'selected' : '' }}>Faculty / Teacher</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>System Administrator</option>
                    </select>
                </div>

                <div class="form-group" id="group-employee-id" style="{{ old('role', $user->role) === 'student' ? 'display:none;' : '' }}">
                    <label for="employee_id" class="form-label">Employee / Faculty ID</label>
                    <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" class="form-input" placeholder="e.g. FAC-2026-0004">
                    <span class="form-hint">Unique institutional ID for payroll and portal</span>
                </div>

                <div class="form-group" id="group-student-id" style="{{ old('role', $user->role) !== 'student' ? 'display:none;' : '' }}">
                    <label for="student_id" class="form-label">Student ID</label>
                    <input type="text" id="student_id" name="student_id" value="{{ old('student_id', $user->student_id) }}" class="form-input" placeholder="e.g. STU-2026-0001">
                    <span class="form-hint">Official student registration number</span>
                </div>
            </div>
        </div>

        {{-- Section 3: Password Management --}}
        <div class="user-edit-section">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Security &amp; Password Override
            </div>
            <div class="user-edit-section-desc">
                Reset or assign a new password for this account. Leave blank to preserve current credentials.
            </div>

            <div class="form-group" style="margin-bottom: 0; max-width: 480px;">
                <label for="password" class="form-label">New Password</label>
                <input type="password" id="password" name="password" class="form-input" placeholder="Enter new password to overwrite..." autocomplete="new-password">
                <span class="form-hint">Must be at least 8 characters. Leave empty if you do not want to alter the existing password.</span>
                @error('password')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Section 4: Account Status & Activation --}}
        <div class="user-edit-section" style="background: rgba(26, 26, 26, 0.7);">
            <div class="user-edit-section-title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                Account Governance
            </div>
            <div class="user-edit-section-desc">
                Enable or suspend portal access permissions for this user
            </div>

            <div>
                <label class="form-toggle-switch">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span class="form-toggle-slider"></span>
                    <div>
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--ivory);">Account is Active &amp; Verified</div>
                        <div style="font-size: 0.75rem; color: var(--text-dim);">When disabled, the user is barred from signing in to the academic portal.</div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex justify-between items-center mt-6" style="padding-top: var(--space-4);">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Save User Updates
            </button>
        </div>
    </form>
</div>

{{-- Interactive Helper Scripts --}}
<script>
function handleRoleChange(role) {
    const empGroup = document.getElementById('group-employee-id');
    const stuGroup = document.getElementById('group-student-id');
    if (role === 'student') {
        empGroup.style.display = 'none';
        stuGroup.style.display = 'block';
    } else {
        empGroup.style.display = 'block';
        stuGroup.style.display = 'none';
    }
}

let isPwRevealed = false;
function togglePasswordVisibility() {
    const pwSpan = document.getElementById('display-password');
    const toggleText = document.getElementById('text-toggle-pw');
    if (!pwSpan) return;

    if (isPwRevealed) {
        pwSpan.textContent = '••••••••••••';
        toggleText.textContent = 'Reveal';
        isPwRevealed = false;
    } else {
        pwSpan.textContent = pwSpan.getAttribute('data-plain');
        toggleText.textContent = 'Hide';
        isPwRevealed = true;
    }
}

function copyText(elementId, btn) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const text = el.innerText.trim();
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <span>Copied!</span>`;
        btn.style.background = 'var(--gold)';
        btn.style.color = '#111';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}

function copyRawPassword(plainText, btn) {
    navigator.clipboard.writeText(plainText).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <span>Copied!</span>`;
        btn.style.background = 'var(--gold)';
        btn.style.color = '#111';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.style.background = '';
            btn.style.color = '';
        }, 2000);
    });
}
</script>
@endsection
