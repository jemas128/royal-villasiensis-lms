@extends('layouts.app')

@section('title', 'Edit Department')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.departments.index') }}">Departments</a>
    <span class="separator">/</span>
    <span class="current">Edit {{ $department->code }}</span>
@endsection

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Edit Department: {{ $department->name }}</h1>
        <p class="page-subtitle">Update department code, title, and operational status</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('admin.departments.update', $department) }}">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="code" class="form-label">Department Code <span class="required">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code', $department->code) }}" class="form-input @error('code') is-invalid @enderror" required>
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Department Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $department->name) }}" class="form-input @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Academic Scope & Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="4">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Active & accepting student enrollments</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
