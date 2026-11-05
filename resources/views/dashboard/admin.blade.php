@extends('layouts.app')

@section('title', 'Admin Executive Overview')

@section('breadcrumb')
    <span class="current">Executive Dashboard</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center flex-wrap gap-4">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">
            Term: <strong class="text-gold">{{ $currentSemester ? $currentSemester->academicYear->name . ' — ' . $currentSemester->name : 'No Active Semester' }}</strong>
        </p>
    </div>
    <div>
        <a href="{{ route('admin.courses.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Course
        </a>
    </div>
</div>

{{-- Top Metrics Grid --}}
<div class="stats-grid mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4);">
    <div class="stat-card accent-gold card" style="padding: var(--space-5);">
        <div class="text-muted text-xs uppercase" style="letter-spacing: 0.05em;">Total Scholars</div>
        <div class="text-ivory text-3xl font-bold mt-2 mb-2 text-serif">{{ $totalStudents }}</div>
        <div class="text-gold text-xs">Active students</div>
    </div>

    <div class="stat-card accent-info card" style="padding: var(--space-5);">
        <div class="text-muted text-xs uppercase" style="letter-spacing: 0.05em;">Academic Faculty</div>
        <div class="text-ivory text-3xl font-bold mt-2 mb-2 text-serif">{{ $totalTeachers }}</div>
        <div class="text-xs" style="color: #5B7F9E;">Instructors & Professors</div>
    </div>

    <div class="stat-card accent-success card" style="padding: var(--space-5);">
        <div class="text-muted text-xs uppercase" style="letter-spacing: 0.05em;">Active Courses</div>
        <div class="text-ivory text-3xl font-bold mt-2 mb-2 text-serif">{{ $totalCourses }}</div>
        <div class="text-xs" style="color: #5B8C5A;">Across {{ $totalDepartments }} Colleges</div>
    </div>

    <div class="stat-card accent-warning card" style="padding: var(--space-5);">
        <div class="text-muted text-xs uppercase" style="letter-spacing: 0.05em;">Total Enrollments</div>
        <div class="text-ivory text-3xl font-bold mt-2 mb-2 text-serif">{{ $totalEnrollments }}</div>
        <div class="text-xs" style="color: #C4973B;">Course slot registrations</div>
    </div>
</div>

{{-- Pending Account Approvals Notice & Quick Action --}}
@if(isset($pendingCount) && $pendingCount > 0)
    <div class="card mb-6" style="border: 1px solid var(--warning-border);">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-2" style="padding: var(--space-5) var(--space-5) 0 var(--space-5);">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center text-gold" style="width: 38px; height: 38px; border-radius: 8px; background: var(--warning-bg);">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-ivory font-semibold text-lg m-0 text-serif">
                        Pending Account Approvals ({{ $pendingCount }})
                    </h2>
                    <p class="text-xs text-muted mt-1 m-0">
                        New scholar registrations awaiting administrative verification and portal clearance.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.users.index', ['status' => 'pending']) }}" class="btn btn-secondary btn-sm">
                View All Pending Requests
            </a>
        </div>

        <div class="table-container" style="border-top: 1px solid var(--border-dark); border-bottom: none; border-left: none; border-right: none; border-radius: 0;">
            <table>
                <thead>
                    <tr>
                        <th>Scholar Name & Email</th>
                        <th>College / Dept</th>
                        <th>Registered</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingUsers as $pu)
                        <tr>
                            <td>
                                <strong class="text-ivory font-semibold">{{ $pu->name }}</strong>
                                <div class="text-xs text-dim">{{ $pu->email }}</div>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $pu->department->code ?? 'General Admission' }}</span>
                            </td>
                            <td class="text-xs text-muted">
                                {{ $pu->created_at->diffForHumans() }}
                            </td>
                            <td class="text-right">
                                <form method="POST" action="{{ route('admin.users.approve', $pu) }}" style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        Approve
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: var(--space-6);">
    {{-- Recent Courses Table --}}
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Recent Courses</h2>
            <a href="{{ route('admin.courses.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Code & Title</th>
                    <th>College</th>
                    <th>Instructor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCourses as $course)
                    <tr>
                        <td>
                            <strong class="text-ivory">{{ $course->code }}</strong>
                            <div class="text-xs text-muted">{{ Str::limit($course->name, 28) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-neutral">{{ $course->department->code ?? '—' }}</span>
                        </td>
                        <td class="text-sm text-secondary">
                            {{ $course->teacher ? $course->teacher->name : 'Unassigned' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted" style="padding: var(--space-6);">No courses registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Recently Registered Users --}}
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Recent System Users</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-ghost btn-sm">View All</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentUsers as $u)
                    <tr>
                        <td>
                            <div class="text-ivory font-medium">{{ $u->name }}</div>
                            <div class="text-xs text-dim">{{ $u->email }}</div>
                        </td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge badge-gold">Admin</span>
                            @elseif($u->role === 'teacher')
                                <span class="badge badge-success">Faculty</span>
                            @else
                                <span class="badge badge-info">Student</span>
                            @endif
                        </td>
                        <td class="text-xs text-muted">
                            {{ $u->created_at->diffForHumans() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted" style="padding: var(--space-6);">No users yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
