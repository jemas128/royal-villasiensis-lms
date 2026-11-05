@extends('layouts.app')

@section('title', 'Manage Assignments')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <span class="current">Assignments</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Coursework Assignments</h1>
        <p class="page-subtitle">Assign deliverables, review submissions, and provide academic grading feedback</p>
    </div>
    <a href="{{ route('teacher.assignments.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Create Assignment
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Course</th>
                <th>Due Date</th>
                <th>Max Points</th>
                <th>Submissions</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assign)
                <tr>
                    <td>
                        <strong style="color: var(--ivory); font-size: var(--text-md);">
                            <a href="{{ route('teacher.assignments.show', $assign) }}" style="color: var(--ivory);">{{ $assign->title }}</a>
                        </strong>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($assign->description, 50) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-gold">{{ $assign->course->code }}</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: {{ $assign->due_date->isPast() ? 'var(--text-muted)' : '#C4973B' }};">
                        {{ $assign->due_date->format('M d, Y h:i A') }}
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $assign->max_score }} pts
                    </td>
                    <td>
                        <a href="{{ route('teacher.assignments.show', $assign) }}" style="color: var(--gold); font-size: var(--text-sm); font-weight: 500;">
                            {{ $assign->submissions_count }} submitted
                        </a>
                    </td>
                    <td>
                        @if($assign->is_published)
                            <span class="badge badge-success">Published</span>
                        @else
                            <span class="badge badge-neutral">Draft</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('teacher.assignments.show', $assign) }}" class="btn btn-primary btn-sm">Submissions</a>
                            <a href="{{ route('teacher.assignments.edit', $assign) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('teacher.assignments.destroy', $assign) }}" onsubmit="return confirm('Delete this assignment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No assignments posted.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $assignments->links() }}
</div>
@endsection
