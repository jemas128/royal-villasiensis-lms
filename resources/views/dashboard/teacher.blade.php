@extends('layouts.app')

@section('title', 'Faculty Portal Dashboard')

@section('breadcrumb')
    <span class="current">Faculty Dashboard</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Faculty Instructional Portal</h1>
        <p class="page-subtitle">Welcome, {{ auth()->user()->name }}. Academic management and scholar evaluation.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('teacher.assignments.create') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Assignment
        </a>
        <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-secondary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Create Quiz
        </a>
    </div>
</div>

{{-- Metrics --}}
<div class="stats-grid mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4);">
    <div class="stat-card accent-gold" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Assigned Courses</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $courses->count() }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--gold);">Active classes this term</div>
    </div>

    <div class="stat-card accent-info" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Enrolled Scholars</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $totalStudents }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #5B7F9E;">Across all your courses</div>
    </div>

    <div class="stat-card accent-warning" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Pending Grading</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $pendingGrading }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #C4973B;">Submissions awaiting review</div>
    </div>

    <div class="stat-card accent-success" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Active Quizzes</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $activeQuizzes }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #5B8C5A;">Published assessments</div>
    </div>
</div>

{{-- Teaching Courses Cards --}}
<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">My Assigned Classes</h2>
        <a href="{{ route('teacher.courses.index') }}" class="btn btn-ghost btn-sm">View All Courses</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-4);">
        @forelse($courses as $course)
            <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="badge badge-gold">{{ $course->code }}</span>
                        <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $course->students_count }} Students</span>
                    </div>
                    <h3 style="font-size: var(--text-md); color: var(--ivory); margin-bottom: var(--space-2);">
                        <a href="{{ route('teacher.courses.show', $course) }}" style="color: var(--ivory);">{{ $course->name }}</a>
                    </h3>
                    <p style="font-size: var(--text-xs); color: var(--text-secondary); margin-bottom: var(--space-3);">
                        {{ Str::limit($course->description, 90) }}
                    </p>
                </div>

                <div style="padding-top: var(--space-3); border-top: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center; font-size: var(--text-xs); color: var(--text-dim);">
                    <span>{{ $course->schedule ?? 'TBA' }}</span>
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-secondary btn-sm">Enter Class</a>
                </div>
            </div>
        @empty
            <div class="card text-center" style="grid-column: 1 / -1; padding: var(--space-8); background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); color: var(--text-muted);">
                You have not been assigned any courses this term.
            </div>
        @endforelse
    </div>
</div>

{{-- Submissions Needing Review --}}
<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Recent Assignment Submissions</h2>
        <a href="{{ route('teacher.assignments.index') }}" class="btn btn-ghost btn-sm">All Assignments</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Scholar</th>
                <th>Assignment & Course</th>
                <th>Submitted</th>
                <th>Status / Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentSubmissions as $submission)
                <tr>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $submission->student->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-dim);">{{ $submission->student->student_id ?? 'No ID' }}</div>
                    </td>
                    <td>
                        <div style="color: var(--ivory);">{{ $submission->assignment->title }}</div>
                        <div style="font-size: var(--text-xs); color: var(--gold);">{{ $submission->assignment->course->code }}</div>
                    </td>
                    <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('M d, h:i A') : '—' }}
                    </td>
                    <td>
                        @if($submission->score !== null)
                            <span class="badge badge-success">Graded: {{ $submission->score }}/{{ $submission->assignment->max_score }}</span>
                        @else
                            <a href="{{ route('teacher.assignments.show', $submission->assignment_id) }}" class="btn btn-primary btn-sm">Grade Now</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: var(--space-6); color: var(--text-muted);">No student submissions received yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
