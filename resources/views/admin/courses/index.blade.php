@extends('layouts.app')

@section('title', 'Manage Courses')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <span class="current">Courses</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Curriculum Courses</h1>
        <p class="page-subtitle">Accredited university courses, schedules, and instructor allocations</p>
    </div>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Course
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Course Title</th>
                <th>Credits</th>
                <th>Department</th>
                <th>Assigned Instructor</th>
                <th>Enrolled</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($courses as $course)
                <tr>
                    <td>
                        <strong style="color: var(--gold); font-family: monospace; font-size: var(--text-md);">{{ $course->code }}</strong>
                    </td>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $course->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-dim);">
                            {{ $course->schedule ?? 'Schedule TBA' }} • {{ $course->room ?? 'Room TBA' }}
                        </div>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $course->credits }} Units
                    </td>
                    <td>
                        <span class="badge badge-neutral">{{ $course->department->code ?? '—' }}</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--ivory);">
                        {{ $course->teacher ? $course->teacher->name : 'Unassigned' }}
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $course->enrollments_count }} students
                    </td>
                    <td>
                        @if($course->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-neutral">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No courses registered yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $courses->links() }}
</div>
@endsection
