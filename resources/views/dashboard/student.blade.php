@extends('layouts.app')

@section('title', 'Student Academic Portal')

@section('breadcrumb')
    <span class="current">Student Dashboard</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="page-subtitle">
            ID: <strong style="color: var(--gold);">{{ auth()->user()->student_id ?? 'STU-NEW' }}</strong> • Term Status: <strong style="color: #5B8C5A;">Enrolled & Active</strong>
        </p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('student.courses.index') }}" class="btn btn-primary btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
            Course Catalog & Registration
        </a>
    </div>
</div>

{{-- Metric Highlights --}}
<div class="stats-grid mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4);">
    <div class="stat-card accent-gold" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Enrolled Courses</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $enrolledCourses->count() }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--gold);">Matriculated subjects</div>
    </div>

    <div class="stat-card accent-warning" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Pending Tasks</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $upcomingAssignments->count() }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #C4973B;">Assignments due soon</div>
    </div>

    <div class="stat-card accent-info" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Available Quizzes</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ count($availableQuizzes) }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #5B7F9E;">Assessments to take</div>
    </div>

    <div class="stat-card accent-success" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Cumulative GPA</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">{{ $gpa ? number_format($gpa, 2) : 'N/A' }}</div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #5B8C5A;">Academic standing</div>
    </div>
</div>

<div class="mb-6">
    <div class="flex justify-between items-center mb-4">
        <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">My Enrolled Courses</h2>
        <a href="{{ route('student.courses.index') }}" class="btn btn-ghost btn-sm">Full Catalog</a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-4);">
        @forelse($enrolledCourses as $course)
            <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="badge badge-gold">{{ $course->code }}</span>
                        <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $course->credits }} Units</span>
                    </div>
                    <h3 style="font-size: var(--text-md); color: var(--ivory); margin-bottom: var(--space-1);">
                        <a href="{{ route('student.courses.show', $course) }}" style="color: var(--ivory);">{{ $course->name }}</a>
                    </h3>
                    <div style="font-size: var(--text-xs); color: var(--text-secondary); margin-bottom: var(--space-3);">
                        Instructor: <strong style="color: var(--ivory);">{{ $course->teacher ? $course->teacher->name : 'Staff' }}</strong>
                    </div>
                </div>

                <div style="padding-top: var(--space-3); border-top: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center; font-size: var(--text-xs); color: var(--text-dim);">
                    <span>{{ $course->schedule ?? 'Room TBA' }}</span>
                    <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary btn-sm">Open Classroom</a>
                </div>
            </div>
        @empty
            <div class="card text-center" style="grid-column: 1 / -1; padding: var(--space-8); background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); color: var(--text-muted);">
                You are not currently enrolled in any courses. Browse the catalog to register.
            </div>
        @endforelse
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: var(--space-6);">
    {{-- Upcoming Assignments --}}
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Pending Assignments</h2>
            <a href="{{ route('student.assignments.index') }}" class="btn btn-ghost btn-sm">All Assignments</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Title & Course</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingAssignments as $assign)
                    <tr>
                        <td>
                            <div style="color: var(--ivory); font-weight: 500;">{{ $assign->title }}</div>
                            <div style="font-size: var(--text-xs); color: var(--gold);">{{ $assign->course->code }}</div>
                        </td>
                        <td style="font-size: var(--text-xs); color: #C4973B;">
                            {{ $assign->due_date->format('M d, Y') }}
                            <div style="font-size: 0.6875rem; color: var(--text-dim);">{{ $assign->due_date->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('student.assignments.show', $assign) }}" class="btn btn-secondary btn-sm">Submit Work</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: var(--space-6); color: var(--text-muted);">No urgent assignments due.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Available Quizzes --}}
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">Online Examinations & Quizzes</h2>
            <a href="{{ route('student.quizzes.index') }}" class="btn btn-ghost btn-sm">All Quizzes</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Exam & Course</th>
                    <th>Time Limit</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($availableQuizzes as $q)
                    <tr>
                        <td>
                            <div style="color: var(--ivory); font-weight: 500;">{{ $q->title }}</div>
                            <div style="font-size: var(--text-xs); color: var(--gold);">{{ $q->course->code }}</div>
                        </td>
                        <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                            {{ $q->duration_minutes }} minutes
                        </td>
                        <td>
                            <a href="{{ route('student.quizzes.show', $q) }}" class="btn btn-primary btn-sm">View Exam</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding: var(--space-6); color: var(--text-muted);">No open quizzes at this time.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
