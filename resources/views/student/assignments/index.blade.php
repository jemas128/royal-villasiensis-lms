@extends('layouts.app')

@section('title', 'My Course Assignments')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <span class="current">Assignments</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Coursework Deliverables</h1>
        <p class="page-subtitle">Track deadlines, submission states, and instructor evaluation results</p>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Assignment Title</th>
                <th>Course</th>
                <th>Deadline</th>
                <th>Score / Max</th>
                <th>Submission Status</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignments as $assign)
                @php
                    $sub = $assign->submissions->first();
                @endphp
                <tr>
                    <td>
                        <strong style="color: var(--ivory); font-size: var(--text-md);">
                            <a href="{{ route('student.assignments.show', $assign) }}" style="color: var(--ivory);">{{ $assign->title }}</a>
                        </strong>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($assign->description, 50) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-gold">{{ $assign->course->code }}</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: {{ $assign->due_date->isPast() ? 'var(--text-muted)' : '#C4973B' }};">
                        {{ $assign->due_date->format('M d, Y h:i A') }}
                    </td>
                    <td>
                        @if($sub && $sub->score !== null)
                            <strong style="color: var(--gold);">{{ $sub->score }}</strong> / {{ $assign->max_score }}
                        @else
                            <span style="color: var(--text-dim);">— / {{ $assign->max_score }}</span>
                        @endif
                    </td>
                    <td>
                        @if($sub && $sub->score !== null)
                            <span class="badge badge-success">Graded</span>
                        @elseif($sub)
                            <span class="badge badge-warning">Submitted</span>
                        @elseif($assign->due_date->isPast())
                            <span class="badge badge-error">Overdue</span>
                        @else
                            <span class="badge badge-neutral">Pending</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('student.assignments.show', $assign) }}" class="btn btn-secondary btn-sm">
                            {{ $sub ? 'View Work' : 'Submit Now' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No assignments posted for your courses.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $assignments->links() }}
</div>
@endsection
