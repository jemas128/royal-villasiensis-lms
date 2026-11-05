@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <span class="current">Edit</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Edit Quiz Parameters: {{ $quiz->title }}</h1>
        <p class="page-subtitle">{{ $quiz->course->code }} — {{ $quiz->course->name }}</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('teacher.quizzes.update', $quiz) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title" class="form-label">Quiz Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $quiz->title) }}" class="form-input" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_minutes" class="form-label">Duration Limit (Minutes) <span class="required">*</span></label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" max="300" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="max_attempts" class="form-label">Max Attempts <span class="required">*</span></label>
                    <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', $quiz->max_attempts) }}" min="1" max="10" class="form-input" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="available_from" class="form-label">Available From</label>
                    <input type="datetime-local" id="available_from" name="available_from" value="{{ old('available_from', $quiz->available_from ? $quiz->available_from->format('Y-m-d\TH:i') : '') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="available_until" class="form-label">Available Until</label>
                    <input type="datetime-local" id="available_until" name="available_until" value="{{ old('available_until', $quiz->available_until ? $quiz->available_until->format('Y-m-d\TH:i') : '') }}" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Exam Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="3">{{ old('description', $quiz->description) }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_published" value="1" class="form-checkbox" {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Published</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Parameters</button>
            </div>
        </form>
    </div>
</div>
@endsection
