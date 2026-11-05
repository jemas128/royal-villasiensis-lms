@extends('layouts.app')

@section('title', 'Create Course')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.courses.index') }}">Courses</a>
    <span class="separator">/</span>
    <span class="current">New</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Register New Course</h1>
        <p class="page-subtitle">Add an accredited course and assign faculty instruction</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('admin.courses.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="code" class="form-label">Course Code <span class="required">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-input @error('code') is-invalid @enderror" placeholder="e.g. CS101, MATH201" required autofocus>
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Course Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input @error('name') is-invalid @enderror" placeholder="e.g. Data Structures & Algorithms" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="department_id" class="form-label">College Department <span class="required">*</span></label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">Select College...</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} — {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="semester_id" class="form-label">Academic Semester <span class="required">*</span></label>
                    <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                        <option value="">Select Term...</option>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ old('semester_id') == $sem->id || $sem->is_current ? 'selected' : '' }}>
                                {{ $sem->academicYear->name }} — {{ $sem->name }} {{ $sem->is_current ? '(Current)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="teacher_id" class="form-label">Assigned Instructor</label>
                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                        <option value="">Select Faculty...</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }} ({{ $teacher->employee_id ?? 'No ID' }})
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="credits" class="form-label">Academic Credits / Units <span class="required">*</span></label>
                    <input type="number" id="credits" name="credits" value="{{ old('credits', 3) }}" min="1" max="10" class="form-input @error('credits') is-invalid @enderror" required>
                    @error('credits')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="schedule" class="form-label">Class Schedule</label>
                    <input type="text" id="schedule" name="schedule" value="{{ old('schedule') }}" class="form-input" placeholder="e.g. Mon / Wed 09:00 - 10:30 AM">
                </div>

                <div class="form-group">
                    <label for="room" class="form-label">Assigned Hall / Lab</label>
                    <input type="text" id="room" name="room" value="{{ old('room') }}" class="form-input" placeholder="e.g. Hall 402 - Turing Lab">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Course Description & Syllabus Summary</label>
                <textarea id="description" name="description" class="form-textarea" rows="4" placeholder="Course outline, prerequisites, learning outcomes...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Course open for student enrollment</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
