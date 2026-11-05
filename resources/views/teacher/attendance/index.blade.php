@extends('layouts.app')

@section('title', 'Classroom Attendance')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <span class="current">Attendance</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Attendance Tracking</h1>
        <p class="page-subtitle">Track classroom attendance records, laboratory presence, and absences</p>
    </div>
</div>

{{-- Course and Date Selector --}}
<div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-4);">
    <form method="GET" action="{{ route('teacher.attendance.index') }}" class="flex gap-4" style="align-items: flex-end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 250px;">
            <label class="form-label" style="margin-bottom: 4px;">Course Section</label>
            <select name="course_id" class="form-select">
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $selectedCourse && $selectedCourse->id == $c->id ? 'selected' : '' }}>
                        {{ $c->code }} — {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div style="min-width: 180px;">
            <label class="form-label" style="margin-bottom: 4px;">Session Date</label>
            <input type="date" name="date" value="{{ $date }}" class="form-input">
        </div>

        <button type="submit" class="btn btn-secondary">Load Session Roster</button>
    </form>
</div>

@if($selectedCourse)
<div class="table-container">
    <div class="table-header">
        <div>
            <h2 class="table-title">{{ $selectedCourse->code }} — Attendance for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h2>
            <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ $roster->count() }} enrolled scholars</div>
        </div>
    </div>

    <form method="POST" action="{{ route('teacher.attendance.store') }}">
        @csrf
        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">
        <input type="hidden" name="date" value="{{ $date }}">

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Scholar Name</th>
                    <th>Attendance Status</th>
                    <th>Session Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roster as $index => $student)
                    @php
                        $attRecord = $attendances->get($student->id);
                        $currentStatus = $attRecord ? $attRecord->status : 'present';
                    @endphp
                    <tr>
                        <td style="font-size: var(--text-sm); font-family: monospace; color: var(--gold);">
                            {{ $student->student_id ?? 'STU-NEW' }}
                        </td>
                        <td>
                            <div style="color: var(--ivory); font-weight: 500;">{{ $student->name }}</div>
                            <input type="hidden" name="attendances[{{ $index }}][student_id]" value="{{ $student->id }}">
                        </td>
                        <td>
                            <div class="flex gap-4" style="align-items: center; font-size: var(--text-xs);">
                                <label style="display: flex; align-items: center; gap: 4px; color: var(--success); cursor: pointer;">
                                    <input type="radio" name="attendances[{{ $index }}][status]" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }} style="accent-color: var(--success);">
                                    Present
                                </label>
                                <label style="display: flex; align-items: center; gap: 4px; color: var(--warning); cursor: pointer;">
                                    <input type="radio" name="attendances[{{ $index }}][status]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }} style="accent-color: var(--warning);">
                                    Late
                                </label>
                                <label style="display: flex; align-items: center; gap: 4px; color: var(--error); cursor: pointer;">
                                    <input type="radio" name="attendances[{{ $index }}][status]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }} style="accent-color: var(--error);">
                                    Absent
                                </label>
                                <label style="display: flex; align-items: center; gap: 4px; color: var(--info); cursor: pointer;">
                                    <input type="radio" name="attendances[{{ $index }}][status]" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }} style="accent-color: var(--info);">
                                    Excused
                                </label>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="attendances[{{ $index }}][remarks]" value="{{ old('attendances.'.$index.'.remarks', $attRecord->remarks ?? '') }}" class="form-input" style="padding: 4px 8px; font-size: var(--text-xs);" placeholder="e.g. Excused with medical note">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No students enrolled in this course section.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($roster->count() > 0)
            <div style="padding: var(--space-4) var(--space-5); background: var(--charcoal); border-top: 1px solid var(--border-dark); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Record Session Attendance
                </button>
            </div>
        @endif
    </form>
</div>
@endif
@endsection
