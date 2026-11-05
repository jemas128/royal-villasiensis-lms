@extends('layouts.app')

@section('title', 'Manage Departments')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <span class="current">Departments</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Colleges & Academic Departments</h1>
        <p class="page-subtitle">Configure faculties, degree programs, and academic divisions</p>
    </div>
    <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Department
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Department Name</th>
                <th>Faculty & Students</th>
                <th>Courses</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($departments as $dept)
                <tr>
                    <td>
                        <strong style="color: var(--gold); font-family: monospace; font-size: var(--text-md);">{{ $dept->code }}</strong>
                    </td>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $dept->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($dept->description, 60) }}</div>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $dept->users_count }} members
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $dept->courses_count }} courses
                    </td>
                    <td>
                        @if($dept->is_active)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-neutral">Inactive</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.departments.edit', $dept) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.departments.destroy', $dept) }}" onsubmit="return confirm('Delete this department?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No departments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $departments->links() }}
</div>
@endsection
