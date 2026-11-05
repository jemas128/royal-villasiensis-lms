@extends('layouts.app')

@section('title', 'Create Department')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <a href="{{ route('admin.departments.index') }}">Departments</a>
    <span class="separator">/</span>
    <span class="current">New</span>
@endsection

@section('content')
<div style="max-width: 680px; margin: 0 auto;">
    <div class="mb-6">
        <h1 class="page-title">Add Academic Department</h1>
        <p class="page-subtitle">Define an academic college, division, or faculty school</p>
    </div>

    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <form method="POST" action="{{ route('admin.departments.store') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="code" class="form-label">Department Code <span class="required">*</span></label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" class="form-input @error('code') is-invalid @enderror" placeholder="e.g. CCS, CBA, CAS" required autofocus>
                    @error('code')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Department Title <span class="required">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input @error('name') is-invalid @enderror" placeholder="e.g. College of Computer Studies" required>
                    @error('name')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Academic Scope & Description</label>
                <textarea id="description" name="description" class="form-textarea" rows="4" placeholder="Overview of disciplines, research focus, and degree offerings...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_active" value="1" class="form-checkbox" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="form-checkbox-label">Active & accepting student enrollments</span>
                </label>
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Department</button>
            </div>
        </form>
    </div>
</div>
@endsection
