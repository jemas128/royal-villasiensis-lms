@extends('layouts.app')

@section('title', 'Users')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <span class="current">Users</span>
@endsection

@section('content')
{{-- Minimalist Header --}}
<div class="mb-5 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-3);">
    <div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <h1 class="page-title" style="margin-bottom: 0;">Users</h1>
            <span class="filter-pill-badge" style="font-size: 0.75rem;">{{ $users->total() }}</span>
        </div>
        <p class="page-subtitle" style="margin-top: 4px; margin-bottom: 0;">
            Manage member accounts and assign roles
        </p>
    </div>
</div>

{{-- Filter Pills --}}
<div style="display: flex; gap: 8px; margin-bottom: var(--space-4); flex-wrap: wrap; align-items: center;">
    <a href="{{ route('admin.users.index') }}" class="filter-pill {{ !request('status') && !request('role') ? 'active' : '' }}">
        All
    </a>
    <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="filter-pill {{ request('status') === 'pending' ? 'active' : '' }}">
        Pending
        @if(isset($pendingCount) && $pendingCount > 0)
            <span class="filter-pill-badge" style="background: var(--gold); color: #111;">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('admin.users.index', ['role' => 'teacher']) }}" class="filter-pill {{ request('role') === 'teacher' ? 'active' : '' }}">
        Teachers
    </a>
    <a href="{{ route('admin.users.index', ['role' => 'student', 'status' => 'active']) }}" class="filter-pill {{ request('role') === 'student' && request('status') === 'active' ? 'active' : '' }}">
        Students
    </a>
    <a href="{{ route('admin.users.index', ['role' => 'admin']) }}" class="filter-pill {{ request('role') === 'admin' ? 'active' : '' }}">
        Admins
    </a>
</div>

{{-- Minimalist Search Bar --}}
<div style="background: rgba(0, 0, 0, 0.35); border: 1px solid rgba(255, 255, 255, 0.07); border-radius: var(--radius-md); padding: 8px 12px; margin-bottom: var(--space-5);">
    <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div style="flex: 1; min-width: 220px; position: relative; display: flex; align-items: center;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="position: absolute; left: 10px; color: var(--text-dim); pointer-events: none;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search by name, email, or ID..." style="padding-left: 32px; height: 36px; font-size: 0.8125rem;">
        </div>
        
        <div style="min-width: 140px;">
            <select name="role" class="form-select" style="height: 36px; font-size: 0.8125rem; padding: 4px 10px;">
                <option value="">All Roles</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-secondary btn-sm" style="height: 36px;">Search</button>

        @if(request()->hasAny(['search', 'role', 'status']))
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm" style="height: 36px;">Reset</a>
        @endif
    </form>
</div>

{{-- Minimalist Table --}}
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>User</th>
                <th>Portal ID</th>
                <th>Department</th>
                <th>Role</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    {{-- User Identity --}}
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="user-avatar-circle">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="color: var(--ivory); font-weight: 600; font-size: 0.875rem;">
                                    {{ $user->name }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Portal ID --}}
                    <td>
                        <span style="font-family: 'SF Mono', Consolas, Monaco, monospace; font-size: 0.8125rem; color: var(--gold-light); font-weight: 600;">
                            {{ $user->portal_id ?? ($user->student_id ?? ($user->employee_id ?? '—')) }}
                        </span>
                    </td>

                    {{-- Department --}}
                    <td>
                        <span style="font-size: 0.8125rem; color: var(--text-secondary);">
                            {{ $user->department ? $user->department->name : '—' }}
                        </span>
                    </td>

                    {{-- Role Switcher --}}
                    <td>
                        @if(auth()->id() === $user->id)
                            <span class="badge badge-gold" title="Your administrative account">Admin (You)</span>
                        @else
                            <form method="POST" action="{{ route('admin.users.change-role', $user) }}" style="margin: 0;">
                                @csrf
                                <select name="role" class="role-select role-{{ $user->role }}" onchange="this.form.submit()" title="Change user role">
                                    <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="teacher" {{ $user->role === 'teacher' ? 'selected' : '' }}>Teacher</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </form>
                        @endif
                    </td>

                    {{-- Account Status --}}
                    <td>
                        @if($user->is_active)
                            <span class="badge" style="background: rgba(91, 140, 90, 0.12); color: #78C277; border: 1px solid rgba(91, 140, 90, 0.25); font-size: 0.7rem;">
                                ● Active
                            </span>
                        @else
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <span class="badge" style="background: rgba(196, 151, 59, 0.12); color: #E5B24D; border: 1px solid rgba(196, 151, 59, 0.25); font-size: 0.7rem;">
                                    Pending
                                </span>
                                <form method="POST" action="{{ route('admin.users.approve', $user) }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-xs" title="Approve and activate account">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>

                    {{-- Minimalist Actions (Delete only, no Edit) --}}
                    <td class="text-right">
                        @if(auth()->id() !== $user->id)
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently remove {{ $user->name }} from directory?')" style="display: inline-block; margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--text-dim); padding: 4px 6px;" title="Delete User">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">
                        No members found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $users->links() }}
</div>
@endsection
