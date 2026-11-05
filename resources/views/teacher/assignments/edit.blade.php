@extends('layouts.app')

@section('title', 'Edit Assignment')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.assignments.index') }}">Assignments</a>
    <span class="separator">/</span>
    <span class="current">Edit</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Edit Assignment: {{ $assignment->title }}</h1>
        <p class="page-subtitle">{{ $assignment->course->code }} — {{ $assignment->course->name }}</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('teacher.assignments.update', $assignment) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="title" class="form-label">Assignment Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $assignment->title) }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="max_score" class="form-label">Maximum Points <span class="required">*</span></label>
                    <input type="number" id="max_score" name="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Submission Deadline <span class="required">*</span></label>
                <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date', $assignment->due_date->format('Y-m-d\TH:i')) }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Instructions & Rubrics</label>
                <textarea id="description" name="description" class="form-textarea" rows="6">{{ old('description', $assignment->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_published" value="1" class="form-checkbox" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Published</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('teacher.assignments.show', $assignment) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection
