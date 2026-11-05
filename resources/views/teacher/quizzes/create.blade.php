@extends('layouts.app')

@section('title', 'Create Quiz')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <span class="current">New</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Configure Assessment Quiz</h1>
        <p class="page-subtitle">Setup assessment parameters, timing constraints, and maximum attempts</p>
    </div>

    @if($courses->isEmpty())
        <div class="alert alert-warning mb-6">
            <strong>No Active Courses Available:</strong> You do not currently have any assigned active courses to create an assessment for. Please ensure you are assigned to a course as an instructor or administrator before proceeding.
        </div>
    @endif

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('teacher.quizzes.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="course_id" class="form-label">Course <span class="required">*</span></label>
                    <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required autofocus>
                        <option value="">Select Course...</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ old('course_id', request('course_id')) == $c->id ? 'selected' : '' }}>
                                {{ $c->code }} — {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="title" class="form-label">Quiz Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-input @error('title') is-invalid @enderror" placeholder="e.g. Midterm Assessment: Core Algorithms" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="duration_minutes" class="form-label">Duration Limit (Minutes) <span class="required">*</span></label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', 30) }}" min="1" max="300" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="max_attempts" class="form-label">Maximum Allowed Attempts <span class="required">*</span></label>
                    <input type="number" id="max_attempts" name="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" max="10" class="form-input" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="available_from" class="form-label">Available From (Optional)</label>
                    <input type="datetime-local" id="available_from" name="available_from" value="{{ old('available_from') }}" class="form-input">
                </div>

                <div class="form-group">
                    <label for="available_until" class="form-label">Available Until (Optional)</label>
                    <input type="datetime-local" id="available_until" name="available_until" value="{{ old('available_until') }}" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Exam Description & Scope</label>
                <textarea id="description" name="description" class="form-textarea" rows="3" placeholder="Topics covered, allowed calculators, honor code statement...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_published" value="1" class="form-checkbox" {{ old('is_published', true) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Publish quiz to students upon question creation</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('teacher.quizzes.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create & Proceed to Questions</button>
            </div>
        </form>
    </div>
</div>
@endsection
