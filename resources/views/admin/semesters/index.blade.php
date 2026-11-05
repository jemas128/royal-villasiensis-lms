@extends('layouts.app')

@section('title', 'Manage Academic Semesters')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}">Admin</a>
    <span class="separator">/</span>
    <span class="current">Semesters</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Academic Years & Semesters</h1>
        <p class="page-subtitle">Schedule academic terms and set the active operational semester</p>
    </div>
    <button type="button" class="btn btn-primary" onclick="document.getElementById('add-semester-modal').style.display='flex'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Semester
    </button>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Academic Year</th>
                <th>Semester Term</th>
                <th>Duration Window</th>
                <th>Courses</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($semesters as $sem)
                <tr>
                    <td>
                        <strong style="color: var(--gold); font-size: var(--text-md);">{{ $sem->academicYear->name }}</strong>
                    </td>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $sem->name }}</div>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $sem->start_date->format('M d, Y') }} — {{ $sem->end_date->format('M d, Y') }}
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $sem->courses_count }} courses
                    </td>
                    <td>
                        @if($sem->is_current)
                            <span class="badge badge-gold" style="font-weight: 700;">★ CURRENT ACTIVE TERM</span>
                        @else
                            <span class="badge badge-neutral">Archived / Scheduled</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            <form method="POST" action="{{ route('admin.semesters.destroy', $sem) }}" onsubmit="return confirm('Remove this semester?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No semesters registered yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $semesters->links() }}
</div>

{{-- Add Semester Modal --}}
<div id="add-semester-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 550px; padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Configure New Semester</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('add-semester-modal').style.display='none'">✕</button>
        </div>

        <form method="POST" action="{{ route('admin.semesters.store') }}">
            @csrf

            <div class="form-group">
                <label for="academic_year" class="form-label">Academic Year <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="academic_year" 
                    name="academic_year" 
                    class="form-input" 
                    placeholder="e.g. 2024-2025" 
                    list="academic_year_options"
                    required
                >
                <datalist id="academic_year_options">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->name }}"></option>
                    @endforeach
                </datalist>
            </div>

            <div class="form-group">
                <label for="sem_name" class="form-label">Semester Term Name <span class="required">*</span></label>
                <input type="text" id="sem_name" name="name" class="form-input" placeholder="e.g. 1st Semester, 2nd Semester, Summer" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="start_date" class="form-label">Term Start Date <span class="required">*</span></label>
                    <input type="date" id="start_date" name="start_date" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="end_date" class="form-label">Term End Date <span class="required">*</span></label>
                    <input type="date" id="end_date" name="end_date" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_current" value="1" class="form-checkbox">
                    <span class="form-checkbox-label">Designate as current active semester</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-semester-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Semester</button>
            </div>
        </form>
    </div>
</div>
@endsection
