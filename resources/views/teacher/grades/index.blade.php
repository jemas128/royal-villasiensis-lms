@extends('layouts.app')

@section('title', 'Academic Gradebook')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <span class="current">Gradebook</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Course Gradebook & Records</h1>
        <p class="page-subtitle">Submit official final grades, letter grades, and academic remarks</p>
    </div>
</div>

{{-- Course Selector --}}
<div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-4);">
    <form method="GET" action="{{ route('teacher.grades.index') }}" class="flex gap-4" style="align-items: flex-end;">
        <div style="flex: 1; max-width: 400px;">
            <label class="form-label" style="margin-bottom: 4px;">Select Course Roster</label>
            <select name="course_id" class="form-select" onchange="this.form.submit()">
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $selectedCourse && $selectedCourse->id == $c->id ? 'selected' : '' }}>
                        {{ $c->code }} — {{ $c->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Load Class Roster</button>
    </form>
</div>

@if($selectedCourse)
<div class="table-container">
    <div class="table-header">
        <div>
            <h2 class="table-title">{{ $selectedCourse->code }} Official Grade Roster</h2>
            <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ $roster->count() }} enrolled scholars</div>
        </div>
    </div>

    <form method="POST" action="{{ route('teacher.grades.store') }}">
        @csrf
        <input type="hidden" name="course_id" value="{{ $selectedCourse->id }}">

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Scholar Name</th>
                    <th style="width: 140px;">Numeric Grade (0-100)</th>
                    <th style="width: 120px;">Letter Grade</th>
                    <th>Academic Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roster as $index => $student)
                    @php
                        $grade = $student->grades->first();
                    @endphp
                    <tr>
                        <td style="font-size: var(--text-sm); font-family: monospace; color: var(--gold);">
                            {{ $student->student_id ?? 'STU-NEW' }}
                        </td>
                        <td>
                            <div style="color: var(--ivory); font-weight: 500;">{{ $student->name }}</div>
                            <div style="font-size: var(--text-xs); color: var(--text-dim);">{{ $student->email }}</div>
                            <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                        </td>
                        <td>
                            <input type="number" step="0.01" min="0" max="100" name="grades[{{ $index }}][numeric_grade]" value="{{ old('grades.'.$index.'.numeric_grade', $grade->numeric_grade ?? '') }}" class="form-input" style="padding: 4px 8px; font-weight: 600;" placeholder="e.g. 95.00">
                        </td>
                        <td>
                            <input type="text" name="grades[{{ $index }}][letter_grade]" value="{{ old('grades.'.$index.'.letter_grade', $grade->letter_grade ?? '') }}" class="form-input" style="padding: 4px 8px;" placeholder="e.g. A, B+">
                        </td>
                        <td>
                            <input type="text" name="grades[{{ $index }}][remarks]" value="{{ old('grades.'.$index.'.remarks', $grade->remarks ?? '') }}" class="form-input" style="padding: 4px 8px;" placeholder="Optional evaluation remarks...">
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No students enrolled in this course yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($roster->count() > 0)
            <div style="padding: var(--space-4) var(--space-5); background: var(--charcoal); border-top: 1px solid var(--border-dark); display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save & Commit Official Grades
                </button>
            </div>
        @endif
    </form>
</div>
@endif
@endsection
