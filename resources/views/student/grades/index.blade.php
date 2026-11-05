@extends('layouts.app')

@section('title', 'Academic Grades & Transcript')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <span class="current">My Grades</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Academic Record & Transcript</h1>
        <p class="page-subtitle">Official semester grading, letter mark evaluations, and cumulative GPA</p>
    </div>
</div>

{{-- GPA Card --}}
<div class="stats-grid mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: var(--space-4);">
    <div class="stat-card accent-gold" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Cumulative GPA</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--gold); margin: var(--space-1) 0;">
            {{ $gpa ? number_format($gpa, 2) : 'N/A' }}
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--ivory);">
            @if($gpa >= 93)
                Dean's List — President's Scholar
            @elseif($gpa >= 88)
                Dean's Honor Roll
            @else
                Good Academic Standing
            @endif
        </div>
    </div>

    <div class="stat-card accent-info" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Graded Courses</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--ivory); margin: var(--space-1) 0;">
            {{ $grades->count() }}
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: #5B7F9E;">Official course records</div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Course Grade Report</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Instructor</th>
                <th>Numeric Mark</th>
                <th>Letter Grade</th>
                <th>Faculty Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($grades as $grade)
                <tr>
                    <td>
                        <strong style="color: var(--gold); font-family: monospace;">{{ $grade->course->code }}</strong>
                    </td>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $grade->course->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ $grade->course->credits }} Academic Units</div>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $grade->course->teacher ? $grade->course->teacher->name : 'Staff' }}
                    </td>
                    <td>
                        @if($grade->numeric_grade !== null)
                            <strong style="color: var(--ivory); font-size: var(--text-md);">{{ number_format($grade->numeric_grade, 2) }}</strong>
                        @else
                            <span style="color: var(--text-dim);">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($grade->letter_grade)
                            <span class="badge badge-gold" style="font-size: var(--text-sm); font-weight: 700; padding: 2px 8px;">
                                {{ $grade->letter_grade }}
                            </span>
                        @else
                            <span class="badge badge-neutral">IP</span>
                        @endif
                    </td>
                    <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                        {{ $grade->remarks ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No official grades posted yet for your coursework.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
