@extends('layouts.app')

@section('title', 'Create Assignment')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.assignments.index') }}">Assignments</a>
    <span class="separator">/</span>
    <span class="current">New</span>
@endsection

@section('content')
<div style="max-width: 780px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Post New Assignment</h1>
        <p class="page-subtitle">Publish a task, research paper, laboratory activity, or milestone deliverable</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('teacher.assignments.store') }}">
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
                    <label for="max_score" class="form-label">Maximum Points <span class="required">*</span></label>
                    <input type="number" id="max_score" name="max_score" value="{{ old('max_score', 100) }}" min="1" class="form-input @error('max_score') is-invalid @enderror" required>
                    @error('max_score')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="title" class="form-label">Assignment Title <span class="required">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-input @error('title') is-invalid @enderror" placeholder="e.g. Laboratory 2: Binary Search Trees Implementation" required>
                @error('title')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Submission Deadline <span class="required">*</span></label>
                <input type="datetime-local" id="due_date" name="due_date" value="{{ old('due_date') }}" class="form-input @error('due_date') is-invalid @enderror" required>
                @error('due_date')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Instructions & Rubrics</label>
                <textarea id="description" name="description" class="form-textarea" rows="6" placeholder="Provide complete assignment guidelines, expected file formats, and submission criteria...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_published" value="1" class="form-checkbox" {{ old('is_published', true) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Publish immediately to enrolled students</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('teacher.assignments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Publish Assignment</button>
            </div>
        </form>
    </div>
</div>
@endsection
