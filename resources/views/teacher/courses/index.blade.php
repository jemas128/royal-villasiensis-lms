@extends('layouts.app')

@section('title', 'My Assigned Courses')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <span class="current">My Courses</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Faculty Assigned Classes</h1>
        <p class="page-subtitle">Your teaching assignments for the current academic term</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: var(--space-5);">
    @forelse($courses as $course)
        <div style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); display: flex; flex-direction: column; justify-content: space-between; overflow: hidden; transition: border-color var(--transition-base);"
             onmouseover="this.style.borderColor='rgba(200,164,93,0.3)'"
             onmouseout="this.style.borderColor='var(--border-dark)'">

            {{-- Card Body --}}
            <div style="padding: var(--space-5);">
                {{-- Top Row --}}
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-3);">
                    <div style="display: flex; align-items: center; gap: var(--space-2);">
                        <span class="badge badge-gold" style="font-size: var(--text-xs); font-weight: 700; letter-spacing: 0.04em;">{{ $course->code }}</span>
                        @if($course->pending_enrollments_count > 0)
                            <span class="badge badge-warning" style="font-size: 0.68rem; animation: pulse 1.5s infinite;">
                                {{ $course->pending_enrollments_count }} Pending
                            </span>
                        @endif
                    </div>
                    <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $course->credits }} Units</span>
                </div>

                {{-- Title --}}
                <h2 style="font-size: var(--text-md); color: var(--ivory); font-family: var(--font-serif); font-weight: 600; margin-bottom: var(--space-2); line-height: 1.35;">
                    <a href="{{ route('teacher.courses.show', $course) }}" style="color: var(--ivory);">{{ $course->name }}</a>
                </h2>

                {{-- Join Code --}}
                <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(200,164,93,0.06); border: 1px dashed rgba(200,164,93,0.3); padding: 3px 10px; border-radius: var(--radius-sm); margin-bottom: var(--space-3);">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span style="font-size: 0.7rem; color: var(--gold-light);">Join Code:</span>
                    <strong style="font-family: monospace; font-size: 0.72rem; color: var(--gold); letter-spacing: 0.08em;">{{ $course->enrollment_code }}</strong>
                </div>

                {{-- Description --}}
                <p style="font-size: var(--text-xs); color: var(--text-muted); line-height: 1.55; margin-bottom: var(--space-4);">
                    {{ Str::limit($course->description, 100) }}
                </p>

                {{-- Meta Grid --}}
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 6px;">
                    {{-- Room --}}
                    <div style="display: flex; align-items: center; gap: 6px; background: var(--charcoal); padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                        <span style="font-size: 0.72rem; color: var(--text-secondary);">{{ $course->room ?? 'TBA' }}</span>
                    </div>
                    {{-- Schedule --}}
                    <div style="display: flex; align-items: center; gap: 6px; background: var(--charcoal); padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span style="font-size: 0.72rem; color: var(--text-secondary);">{{ $course->schedule ?? 'TBA' }}</span>
                    </div>
                    {{-- Students --}}
                    <div style="display: flex; align-items: center; gap: 6px; background: var(--charcoal); padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span style="font-size: 0.72rem; color: var(--text-secondary);">{{ $course->students_count }} Enrolled</span>
                    </div>
                    {{-- Materials --}}
                    <div style="display: flex; align-items: center; gap: 6px; background: var(--charcoal); padding: 7px 10px; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--text-muted)" stroke-width="2" stroke-linecap="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        <span style="font-size: 0.72rem; color: var(--text-secondary);">{{ $course->materials_count }} Materials</span>
                    </div>
                </div>
            </div>

            {{-- Card Footer --}}
            <div style="padding: var(--space-3) var(--space-5); border-top: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.15);">
                <span class="badge badge-neutral" style="font-size: 0.68rem;">{{ $course->semester->name ?? 'Term' }}</span>
                <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 5px;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    Manage Classroom
                    @if($course->pending_enrollments_count > 0)
                        <span style="background: var(--warning); color: #000; border-radius: 50%; width: 15px; height: 15px; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700;">{{ $course->pending_enrollments_count }}</span>
                    @endif
                </a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: var(--space-12); background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); color: var(--text-muted);">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto var(--space-3);"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
            <div style="font-size: var(--text-sm); color: var(--text-dim);">No courses assigned to your faculty profile this term.</div>
        </div>
    @endforelse
</div>
@endsection
