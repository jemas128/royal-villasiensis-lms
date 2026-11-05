@extends('layouts.app')

@section('title', 'Manage Enrollments')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <span class="current">Enrollments</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Course Enrollments & Matriculation</h1>
        <p class="page-subtitle">Track and configure student course rosters</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('enroll-student-modal').style.display='flex'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
        Enroll Student
    </button>
</div>

{{-- Filter by Course --}}
<div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-4);">
    <form method="GET" action="{{ route('admin.enrollments.index') }}" class="flex gap-4" style="align-items: flex-end;">
        <div style="flex: 1; max-width: 380px;">
            <label class="form-label" style="margin-bottom: 4px;">Filter by Course</label>
            <select name="course_id" class="form-select">
                <option value="">All Courses</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->code }} — {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if(request('course_id'))
            <a href="{{ route('admin.enrollments.index') }}" class="btn btn-ghost">Reset</a>
        @endif
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Scholar Name</th>
                <th>Student ID</th>
                <th>Enrolled Course</th>
                <th>Instructor</th>
                <th>Status</th>
                <th>Enrolled On</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($enrollments as $enr)
                <tr>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $enr->student->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-dim);">{{ $enr->student->email }}</div>
                    </td>
                    <td style="font-size: var(--text-sm); font-family: monospace; color: var(--gold);">
                        {{ $enr->student->student_id ?? '—' }}
                    </td>
                    <td>
                        <strong style="color: var(--ivory);">{{ $enr->course->code }}</strong>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($enr->course->name, 25) }}</div>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $enr->course->teacher ? $enr->course->teacher->name : 'Unassigned' }}
                    </td>
                    <td>
                        @if($enr->status === 'enrolled')
                            <span class="badge badge-success">Enrolled</span>
                        @elseif($enr->status === 'dropped')
                            <span class="badge badge-error">Dropped</span>
                        @else
                            <span class="badge badge-gold">Completed</span>
                        @endif
                    </td>
                    <td style="font-size: var(--text-xs); color: var(--text-muted);">
                        {{ $enr->created_at->format('M d, Y') }}
                    </td>
                    <td class="text-right">
                        <form method="POST" action="{{ route('admin.enrollments.destroy', $enr) }}" onsubmit="return confirm('Remove this student enrollment record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No enrollments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $enrollments->links() }}
</div>

{{-- Enroll Student Modal --}}
<div id="enroll-student-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 550px; padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Enroll Student in Course</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('enroll-student-modal').style.display='none'">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.enrollments.store') }}">
            @csrf

            <div class="form-group">
                <label for="student_id_select" class="form-label">Select Student <span class="required">*</span></label>
                <select name="student_id" id="student_id_select" class="form-select" required>
                    <option value="">Choose Student...</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->student_id ?? $student->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="course_id_select" class="form-label">Select Course <span class="required">*</span></label>
                <select name="course_id" id="course_id_select" class="form-select" required>
                    <option value="">Choose Course...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status_select" class="form-label">Enrollment Status</label>
                <select name="status" id="status_select" class="form-select">
                    <option value="enrolled">Enrolled</option>
                    <option value="dropped">Dropped</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('enroll-student-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Enroll Student</button>
            </div>
        </form>
    </div>
</div>
@endsection
