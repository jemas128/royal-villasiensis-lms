@extends('layouts.app')

@section('title', 'Edit Course')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.courses.index') }}">Courses</a>
    <span class="separator">/</span>
    <span class="current">Edit {{ $course->code }}</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Edit Course: {{ $course->code }}</h1>
        <p class="page-subtitle">{{ $course->name }}</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('admin.courses.update', $course) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="code" class="form-label">Course Code <span class="required">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $course->code) }}" class="form-input @error('code') is-invalid @enderror" required>
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Course Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $course->name) }}" class="form-input @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="department_id" class="form-label">College Department <span class="required">*</span></label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $course->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->code }} — {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester_id" class="form-label">Academic Semester <span class="required">*</span></label>
                    <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ old('semester_id', $course->semester_id) == $sem->id ? 'selected' : '' }}>
                                {{ $sem->academicYear->name }} — {{ $sem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="teacher_id" class="form-label">Assigned Instructor</label>
                    <select name="teacher_id" id="teacher_id" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $course->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="credits" class="form-label">Credits / Units <span class="required">*</span></label>
                    <input type="number" id="credits" name="credits" value="{{ old('credits', $course->credits) }}" min="1" max="10" class="form-input" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="schedule" class="form-label">Class Schedule</label>
                    <input type="text" id="schedule" name="schedule" value="{{ old('schedule', $course->schedule) }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="room" class="form-label">Assigned Room</label>
                    <input type="text" id="room" name="room" value="{{ old('room', $course->room) }}" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Course Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="4">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Course active and available for enrollment</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Course</button>
            </div>
        </form>
    </div>
</div>
@endsection
