@extends('layouts.app')

@section('title', 'Attendance Record')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <span class="current">Attendance</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Classroom Attendance Log</h1>
        <p class="page-subtitle">Personal attendance record across all enrolled lecture sections</p>
    </div>
</div>

{{-- Attendance Stats --}}
<div class="stats-grid mb-6" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4);">
    <div class="stat-card accent-gold" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Presence Rate</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: var(--gold); margin: var(--space-1) 0;">
            {{ $attendanceRate }}%
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--ivory);">Classroom attendance</div>
    </div>

    <div class="stat-card accent-success" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Sessions Present</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: #5B8C5A; margin: var(--space-1) 0;">
            {{ $totalPresent }}
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--text-muted);">Recorded present days</div>
    </div>

    <div class="stat-card accent-warning" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Late Arrivals</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: #C4973B; margin: var(--space-1) 0;">
            {{ $totalLate }}
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--text-muted);">Recorded late sessions</div>
    </div>

    <div class="stat-card accent-error" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
        <div class="stat-label" style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Unexcused Absences</div>
        <div class="stat-value" style="font-family: var(--font-serif); font-size: var(--text-3xl); font-weight: 700; color: #B85C5C; margin: var(--space-1) 0;">
            {{ $totalAbsent }}
        </div>
        <div class="stat-change" style="font-size: var(--text-xs); color: var(--text-muted);">Recorded missed days</div>
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Daily Attendance History</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Course</th>
                <th>Status</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $att)
                <tr>
                    <td style="font-size: var(--text-sm); color: var(--ivory);">
                        {{ $att->date->format('M d, Y (l)') }}
                    </td>
                    <td>
                        <strong style="color: var(--gold);">{{ $att->course->code }}</strong> — {{ $att->course->name }}
                    </td>
                    <td>
                        @if($att->status === 'present')
                            <span class="badge badge-success">Present</span>
                        @elseif($att->status === 'late')
                            <span class="badge badge-warning">Late</span>
                        @elseif($att->status === 'excused')
                            <span class="badge badge-info">Excused</span>
                        @else
                            <span class="badge badge-error">Absent</span>
                        @endif
                    </td>
                    <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                        {{ $att->remarks ?? '—' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No attendance records recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $attendances->links() }}
</div>
@endsection
