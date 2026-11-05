@extends('layouts.app')

@section('title', 'Courses')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <span class="current">Courses</span>
@endsection

@section('content')
{{-- Page Title Header --}}
<div class="mb-5">
    <h1 class="page-title" style="margin-bottom: 2px;">Courses</h1>
    <p class="page-subtitle" style="margin-bottom: 0;">Enrolled subjects and curriculum directory</p>
</div>

{{-- Join Class via Code Box --}}
<div class="card mb-6" style="background: rgba(0, 0, 0, 0.35); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: var(--radius-md); padding: 14px 18px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div>
            <div style="font-size: 0.95rem; font-weight: 600; color: var(--ivory); margin-bottom: 2px;">
                Join a Class
            </div>
            <div style="font-size: 0.775rem; color: var(--text-muted);">
                Enter the subject code provided by your teacher
            </div>
        </div>

        <form method="POST" action="{{ route('student.courses.join') }}" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            @csrf
            <div>
                <input 
                    type="text" 
                    id="join-code-input"
                    name="code" 
                    class="form-input" 
                    placeholder="e.g. CS201-EPSO" 
                    value="{{ old('code') }}"
                    required 
                    style="font-family: 'SF Mono', Consolas, Monaco, monospace; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; width: 180px; height: 36px; font-size: 0.8125rem; padding: 6px 10px;"
                >
                @error('code')
                    <div class="form-error" style="font-size: 0.7rem; margin-top: 2px;">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="height: 36px;">
                Join Class
            </button>
        </form>
    </div>
</div>

{{-- Pending Approvals Queue --}}
@if($pendingEnrollments->count() > 0)
<div class="card mb-6" style="background: rgba(200, 164, 93, 0.04); border: 1px solid rgba(200, 164, 93, 0.25); border-radius: var(--radius-md); padding: 14px 18px;">
    <div class="flex items-center gap-2 mb-3">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span style="font-size: 0.875rem; color: var(--ivory); font-weight: 600;">
            Pending Approvals ({{ $pendingEnrollments->count() }})
        </span>
    </div>

    <div style="display: flex; flex-direction: column; gap: 8px;">
        @foreach($pendingEnrollments as $pending)
            <div style="background: rgba(0, 0, 0, 0.35); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: var(--radius-sm); padding: 10px 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <span class="badge badge-gold" style="font-size: 0.7rem;">{{ $pending->course->code }}</span>
                    <strong style="color: var(--ivory); font-size: 0.875rem; margin-left: 6px;">{{ $pending->course->name }}</strong>
                    <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 8px;">
                        Teacher: {{ $pending->course->teacher ? $pending->course->teacher->name : 'Staff' }}
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <span class="badge" style="background: rgba(196, 151, 59, 0.15); color: #E5B24D; font-size: 0.7rem;">Awaiting Review</span>
                    <form method="POST" action="{{ route('student.courses.cancel-request', $pending) }}" onsubmit="return confirm('Cancel request for {{ $pending->course->code }}?')" style="margin: 0;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-ghost btn-xs" style="color: var(--error);">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Enrolled Courses Section --}}
<div class="mb-8">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
        <h2 style="font-size: 1.05rem; color: var(--ivory); font-weight: 600; margin: 0;">
            Enrolled Courses ({{ $enrolledCourses->count() }})
        </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: var(--space-4);">
        @forelse($enrolledCourses as $course)
            <div class="card" style="background: rgba(26, 25, 23, 0.9); border: 1px solid var(--border-dark); border-radius: var(--radius-md); padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    {{-- Badges --}}
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex gap-2 items-center">
                            <span class="badge badge-gold" style="font-weight: 700;">{{ $course->code }}</span>
                            <span class="badge badge-neutral">{{ $course->department->code ?? 'DEP' }}</span>
                        </div>
                        <span class="badge badge-success" style="font-size: 0.7rem;">Enrolled</span>
                    </div>

                    {{-- Title --}}
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: var(--ivory); margin-bottom: 6px; line-height: 1.4;">
                        <a href="{{ route('student.courses.show', $course) }}" style="color: var(--ivory);">{{ $course->name }}</a>
                    </h3>

                    {{-- Teacher with clean SVG icon --}}
                    <div style="font-size: 0.775rem; color: var(--text-secondary); margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--gold); flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>{{ $course->teacher ? $course->teacher->name : 'Faculty Staff' }}</span>
                        <span>•</span>
                        <span>{{ $course->credits }} Units</span>
                    </div>

                    {{-- Class Metrics --}}
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 6px; padding: 8px 10px; background: rgba(0, 0, 0, 0.35); border-radius: var(--radius-sm); border: 1px solid rgba(255, 255, 255, 0.05); margin-bottom: 12px; text-align: center;">
                        <div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Coursework</div>
                            <div style="font-size: 0.775rem; font-weight: 600; color: var(--gold);">{{ $course->assignments_count }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Quizzes</div>
                            <div style="font-size: 0.775rem; font-weight: 600; color: var(--info);">{{ $course->quizzes_count }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase;">Materials</div>
                            <div style="font-size: 0.775rem; font-weight: 600; color: var(--success);">{{ $course->materials_count }}</div>
                        </div>
                    </div>
                </div>

                {{-- Action Footer --}}
                <div style="padding-top: 10px; border-top: 1px solid rgba(255, 255, 255, 0.06); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.72rem; color: var(--text-dim);">
                        {{ $course->schedule ?? 'Regular' }}
                    </span>
                    <a href="{{ route('student.courses.show', $course) }}" class="btn btn-primary btn-xs" style="padding: 5px 12px;">
                        Enter Classroom →
                    </a>
                </div>
            </div>
        @empty
            <div class="academic-empty-state" style="grid-column: 1 / -1; padding: 32px 16px; text-align: center;">
                <div style="font-size: 0.95rem; color: var(--ivory); font-weight: 600; margin-bottom: 4px;">No Enrolled Courses</div>
                <div style="font-size: 0.8125rem; color: var(--text-muted);">Enter an enrollment code above to request admission to your classes.</div>
            </div>
        @endforelse
    </div>
</div>

{{-- University Curriculum Directory --}}
@if($availableCourses->count() > 0)
<div>
    <div style="margin-bottom: 12px;">
        <h2 style="font-size: 1.05rem; color: var(--ivory); font-weight: 600; margin: 0;">
            Course Directory
        </h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: var(--space-4);">
        @foreach($availableCourses as $course)
            <div class="card" style="background: rgba(22, 21, 20, 0.95); border: 1px solid var(--border-dark); border-radius: var(--radius-md); padding: 14px 16px; display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="badge badge-neutral" style="font-weight: 600;">{{ $course->code }}</span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $course->credits }} Units</span>
                    </div>

                    <h3 style="font-size: 0.9rem; font-weight: 600; color: var(--ivory); margin-bottom: 4px;">
                        {{ $course->name }}
                    </h3>

                    <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--gold); flex-shrink: 0;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span>{{ $course->teacher ? $course->teacher->name : 'Staff' }}</span>
                        <span>•</span>
                        <span>{{ $course->department->name ?? 'General' }}</span>
                    </div>
                </div>

                <div style="padding-top: 10px; border-top: 1px solid rgba(255, 255, 255, 0.05); display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.7rem; color: var(--text-dim);">{{ $course->schedule ?? 'TBA' }}</span>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="focusJoinCode()">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Requires Code
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

<script>
function focusJoinCode() {
    const input = document.getElementById('join-code-input');
    if (input) {
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
        input.focus();
        input.classList.add('is-invalid');
        setTimeout(() => input.classList.remove('is-invalid'), 1200);
    }
}
</script>
@endsection
