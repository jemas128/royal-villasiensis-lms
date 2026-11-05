@extends('layouts.app')

@section('title', $course->code . ' — ' . $course->name)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <a href="{{ route('student.courses.index') }}">Courses</a>
    <span class="separator">/</span>
    <span class="current">{{ $course->code }}</span>
@endsection

@section('content')
{{-- =========================================================================
     CLASSROOM HERO BANNER
     ========================================================================= --}}
<div class="classroom-hero">
    <div class="classroom-hero-header">
        <div class="classroom-hero-main">
            <div class="classroom-badge-row">
                <span class="classroom-code-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                    {{ $course->code }}
                </span>
                <span class="badge badge-gold">{{ $course->department->code ?? 'ACAD' }}</span>
                <span class="badge badge-neutral" style="background: rgba(255,255,255,0.06); color: var(--text-secondary); border: 1px solid var(--border-dark);">
                    {{ $course->semester->name ?? 'Current Term' }}
                    @if($course->semester && $course->semester->academicYear)
                        • {{ $course->semester->academicYear->name }}
                    @endif
                </span>
                <span class="badge badge-success">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right: 4px;">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Enrolled Scholar
                </span>
            </div>

            <h1 class="classroom-hero-title">{{ $course->name }}</h1>

            <p class="classroom-hero-desc">
                {{ $course->description ?? 'Comprehensive coursework and curriculum syllabus under the Department of ' . ($course->department->name ?? 'Academic Studies') . '.' }}
            </p>
        </div>

        {{-- Faculty Instructor Spotlight Card --}}
        <div class="faculty-spotlight-card">
            <div class="faculty-avatar-ring">
                @if($course->teacher)
                    {{ strtoupper(substr($course->teacher->name, 0, 1)) }}
                @else
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9v1M15 9v1M9 13v1M15 13v1"/></svg>
                @endif
            </div>
            <div>
                <div class="faculty-info-sub">Faculty Instructor</div>
                <div class="faculty-info-name">
                    {{ $course->teacher ? $course->teacher->name : 'Staff Instructor' }}
                </div>
                <div class="faculty-info-meta">
                    {{ $course->department->name ?? 'Faculty of Arts & Sciences' }}
                </div>
                @if($course->teacher && $course->teacher->email)
                    <div style="font-size: var(--text-xs); margin-top: 4px;">
                        <a href="mailto:{{ $course->teacher->email }}" style="color: var(--gold-light); display: inline-flex; align-items: center; gap: 4px;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                            {{ $course->teacher->email }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Course Logistics Strip with Crisp SVG Icons --}}
    <div class="classroom-meta-grid">
        {{-- Schedule --}}
        <div class="classroom-meta-tile">
            <div class="classroom-meta-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="classroom-meta-content">
                <div class="classroom-meta-label">Lecture Schedule</div>
                <div class="classroom-meta-value">{{ $course->schedule ?? 'Regular Term Sessions' }}</div>
            </div>
        </div>

        {{-- Hall / Location --}}
        <div class="classroom-meta-tile">
            <div class="classroom-meta-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9v1M15 9v1M9 13v1M15 13v1M9 17v1M15 17v1"/>
                </svg>
            </div>
            <div class="classroom-meta-content">
                <div class="classroom-meta-label">Campus Hall / Room</div>
                <div class="classroom-meta-value">{{ $course->room ?? 'Academic Building / TBA' }}</div>
            </div>
        </div>

        {{-- Academic Units --}}
        <div class="classroom-meta-tile">
            <div class="classroom-meta-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div class="classroom-meta-content">
                <div class="classroom-meta-label">Course Credits</div>
                <div class="classroom-meta-value">{{ $course->credits }} Academic Units</div>
            </div>
        </div>

        {{-- Enrolled Peers --}}
        <div class="classroom-meta-tile">
            <div class="classroom-meta-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div class="classroom-meta-content">
                <div class="classroom-meta-label">Class Scholars</div>
                <div class="classroom-meta-value">{{ $course->students->count() }} Peers Registered</div>
            </div>
        </div>
    </div>
</div>

{{-- =========================================================================
     CLASSROOM TABBED WORKSPACE
     ========================================================================= --}}
<div class="classroom-tabs-bar" role="tablist">
    <button type="button" class="classroom-tab-btn active" data-tab="tab-overview" role="tab" aria-selected="true">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="16" x2="12" y2="12"/>
            <line x1="12" y1="8" x2="12.01" y2="8"/>
        </svg>
        Overview & Syllabus
    </button>
    <button type="button" class="classroom-tab-btn" data-tab="tab-assignments" role="tab" aria-selected="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
        </svg>
        Assignments
        <span class="classroom-tab-count">{{ $course->assignments->count() }}</span>
    </button>
    <button type="button" class="classroom-tab-btn" data-tab="tab-quizzes" role="tab" aria-selected="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
        </svg>
        Assessments & Quizzes
        <span class="classroom-tab-count">{{ $course->quizzes->count() }}</span>
    </button>
    <button type="button" class="classroom-tab-btn" data-tab="tab-materials" role="tab" aria-selected="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
        </svg>
        Learning Materials
        <span class="classroom-tab-count">{{ $course->materials->count() }}</span>
    </button>
    <button type="button" class="classroom-tab-btn" data-tab="tab-announcements" role="tab" aria-selected="false">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
        </svg>
        Announcements
        <span class="classroom-tab-count">{{ $course->announcements->count() }}</span>
    </button>
</div>

{{-- -------------------------------------------------------------------------
     TAB 1: OVERVIEW & SYLLABUS
     ------------------------------------------------------------------------- --}}
<div id="tab-overview" class="tab-pane active" role="tabpanel">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6);">
        {{-- Left column: Syllabus and Academic Objectives --}}
        <div>
            <div class="card mb-6" style="padding: var(--space-6);">
                <div class="flex items-center gap-3 mb-4">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gold-glow); color: var(--gold); display: flex; align-items: center; justify-content: center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    </div>
                    <div>
                        <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: 2px;">
                            Curriculum Syllabus & Course Objectives
                        </h2>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">
                            Official academic program outline approved by the Department
                        </div>
                    </div>
                </div>

                <div style="font-size: var(--text-sm); color: var(--text-secondary); line-height: 1.7; margin-bottom: var(--space-5);">
                    <p>{{ $course->description ?? 'This curriculum delivers comprehensive instructional training designed to develop technical competencies and academic rigorous scholarship. Students must review assigned readings prior to lectures and complete all assessments on time.' }}</p>
                </div>

                <div style="background: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-dark); border-radius: var(--radius-md); padding: var(--space-4);">
                    <div style="font-size: var(--text-xs); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--gold); margin-bottom: var(--space-2);">
                        Classroom Guidelines & Policy
                    </div>
                    <ul style="font-size: var(--text-xs); color: var(--text-secondary); line-height: 1.8; margin-left: var(--space-4);">
                        <li>Regular attendance and class participation are recorded weekly.</li>
                        <li>Assignments submitted past the specified deadline may incur academic deductions.</li>
                        <li>Ensure all uploaded coursework conforms to University academic integrity standards.</li>
                    </ul>
                </div>
            </div>

            {{-- Coursework Quick Preview --}}
            <div class="card" style="padding: var(--space-6);">
                <div class="flex justify-between items-center mb-4">
                    <h3 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Upcoming Deadlines & Tasks</h3>
                    <a href="javascript:void(0)" onclick="switchTab('tab-assignments')" style="font-size: var(--text-xs); color: var(--gold);">View All Coursework →</a>
                </div>

                @php
                    $pendingAssignments = $course->assignments->filter(function($a) {
                        return $a->submissions->isEmpty();
                    })->take(3);
                @endphp

                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    @forelse($pendingAssignments as $assign)
                        <div class="classroom-item-card">
                            <div class="flex items-center gap-3">
                                <div class="classroom-item-icon icon-assignment">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div>
                                    <div style="color: var(--ivory); font-weight: 500; font-size: var(--text-sm);">
                                        <a href="{{ route('student.assignments.show', $assign) }}" style="color: var(--ivory);">{{ $assign->title }}</a>
                                    </div>
                                    <div style="font-size: var(--text-xs); color: var(--text-muted); margin-top: 2px;">
                                        Due {{ $assign->due_date ? $assign->due_date->format('M d, Y • h:i A') : 'No due date' }} • {{ $assign->max_score }} Points
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('student.assignments.show', $assign) }}" class="btn btn-secondary btn-sm">
                                Submit Work
                            </a>
                        </div>
                    @empty
                    <div style="text-align: center; padding: var(--space-6); color: var(--text-muted); font-size: var(--text-xs); display: flex; flex-direction: column; align-items: center; gap: 6px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="20 6 9 17 4 12"/></svg>
                            You have no pending assignments waiting for submission!
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right column: Class Summary & Fast Links --}}
        <div>
            {{-- Quick Progress / Metrics --}}
            <div class="card mb-6" style="padding: var(--space-5);">
                <div style="font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--gold); font-weight: 600; margin-bottom: var(--space-4);">
                    Classroom Activity
                </div>

                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-3); background: rgba(255,255,255,0.02); border-radius: var(--radius-md);">
                        <span style="font-size: var(--text-xs); color: var(--text-secondary);">Coursework Assignments</span>
                        <span class="badge badge-gold">{{ $course->assignments->count() }} Total</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-3); background: rgba(255,255,255,0.02); border-radius: var(--radius-md);">
                        <span style="font-size: var(--text-xs); color: var(--text-secondary);">Published Quizzes</span>
                        <span class="badge badge-info">{{ $course->quizzes->count() }} Active</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-3); background: rgba(255,255,255,0.02); border-radius: var(--radius-md);">
                        <span style="font-size: var(--text-xs); color: var(--text-secondary);">Lecture Documents</span>
                        <span class="badge badge-success">{{ $course->materials->count() }} Files</span>
                    </div>
                </div>
            </div>

            {{-- Course Withdrawal / Actions --}}
            <div class="card" style="padding: var(--space-5); border: 1px solid rgba(184, 92, 92, 0.25);">
                <div style="font-size: var(--text-xs); text-transform: uppercase; letter-spacing: 0.05em; color: var(--error); font-weight: 600; margin-bottom: var(--space-2);">
                    Course Enrollment Actions
                </div>
                <p style="font-size: var(--text-xs); color: var(--text-muted); line-height: 1.5; margin-bottom: var(--space-4);">
                    Need to modify your curriculum schedule? You can officially withdraw from this course.
                </p>
                <form method="POST" action="{{ route('student.courses.drop', $course) }}" onsubmit="return confirm('Confirm class withdrawal: Are you sure you wish to drop {{ $course->code }}?')">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-full">
                        Drop This Course
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- -------------------------------------------------------------------------
     TAB 2: COURSEWORK & ASSIGNMENTS
     ------------------------------------------------------------------------- --}}
<div id="tab-assignments" class="tab-pane" role="tabpanel">
    <div class="card" style="padding: var(--space-6);">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: 2px;">
                    Academic Coursework & Assignments
                </h2>
                <p style="font-size: var(--text-xs); color: var(--text-muted);">
                    Submit and review your homework, project deliverables, and lab reports
                </p>
            </div>
            <span class="badge badge-gold">{{ $course->assignments->count() }} Assigned</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @forelse($course->assignments as $assign)
                @php
                    $submission = $assign->submissions->first();
                @endphp
                <div class="classroom-item-card">
                    <div class="flex items-center gap-4" style="flex: 1; min-width: 260px;">
                        <div class="classroom-item-icon icon-assignment">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <div>
                            <div style="color: var(--ivory); font-weight: 600; font-size: var(--text-sm);">
                                <a href="{{ route('student.assignments.show', $assign) }}" style="color: var(--ivory);">{{ $assign->title }}</a>
                            </div>
                            <div style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 3px; line-height: 1.4;">
                                {{ Str::limit($assign->description, 100) }}
                            </div>
                        <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; display: flex; gap: var(--space-3); flex-wrap: wrap;">
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                    Due: <strong>{{ $assign->due_date ? $assign->due_date->format('M d, Y • h:i A') : 'TBA' }}</strong></span>
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                    Weight: <strong>{{ $assign->max_score }} pts</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($submission && $submission->score !== null)
                            <div style="text-align: right;">
                                <span class="badge badge-success" style="font-size: var(--text-xs);">
                                    Graded: {{ $submission->score }} / {{ $assign->max_score }}
                                </span>
                            </div>
                        @elseif($submission)
                            <span class="badge badge-warning">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right: 3px;"><polyline points="20 6 9 17 4 12"/></svg>
                                Submitted
                            </span>
                        @else
                            <span class="badge badge-neutral" style="background: rgba(255,255,255,0.06); color: var(--text-muted);">
                                Pending Work
                            </span>
                        @endif

                        <a href="{{ route('student.assignments.show', $assign) }}" class="btn {{ $submission ? 'btn-secondary' : 'btn-primary' }} btn-sm">
                            {{ $submission ? 'View Submission' : 'Submit Work' }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="academic-empty-state">
                    <div class="academic-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                        </svg>
                    </div>
                    <div class="academic-empty-title">No Coursework Assigned Yet</div>
                    <div class="academic-empty-desc">
                        Your instructor has not published any coursework assignments for this subject. New assignments will appear here once released.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- -------------------------------------------------------------------------
     TAB 3: ASSESSMENTS & QUIZZES
     ------------------------------------------------------------------------- --}}
<div id="tab-quizzes" class="tab-pane" role="tabpanel">
    <div class="card" style="padding: var(--space-6);">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: 2px;">
                    Online Assessments & Quizzes
                </h2>
                <p style="font-size: var(--text-xs); color: var(--text-muted);">
                    Timed examinations, chapter evaluations, and knowledge checkpoints
                </p>
            </div>
            <span class="badge badge-info">{{ $course->quizzes->count() }} Quizzes</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @forelse($course->quizzes as $quiz)
                @php
                    $myAttempts = $quiz->attempts->where('student_id', auth()->id());
                    $bestAttempt = $myAttempts->sortByDesc('score')->first();
                    $attemptsCount = $myAttempts->whereNotNull('completed_at')->count();
                @endphp
                <div class="classroom-item-card">
                    <div class="flex items-center gap-4" style="flex: 1; min-width: 260px;">
                        <div class="classroom-item-icon icon-quiz">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <div>
                            <div style="color: var(--ivory); font-weight: 600; font-size: var(--text-sm);">
                                <a href="{{ route('student.quizzes.show', $quiz) }}" style="color: var(--ivory);">{{ $quiz->title }}</a>
                            </div>
                            <div style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 3px;">
                                {{ Str::limit($quiz->description, 100) }}
                            </div>
                            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; display: flex; gap: var(--space-3); flex-wrap: wrap;">
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                    <strong>{{ $quiz->duration_minutes }} min</strong></span>
                                <span style="display:inline-flex;align-items:center;gap:4px;">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                                    Attempts: <strong>{{ $attemptsCount }} / {{ $quiz->max_attempts }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($bestAttempt)
                            <div style="text-align: right;">
                                <span class="badge badge-gold" style="font-size: var(--text-xs);">
                                    Best: {{ round($bestAttempt->percentage, 1) }}%
                                </span>
                            </div>
                        @endif

                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn btn-primary btn-sm">
                            {{ $attemptsCount > 0 ? 'Review / Retake' : 'Begin Assessment' }}
                        </a>
                    </div>
                </div>
            @empty
                <div class="academic-empty-state">
                    <div class="academic-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="academic-empty-title">No Quizzes Scheduled</div>
                    <div class="academic-empty-desc">
                        No online assessments or midterm quizzes are currently active for this class. Check back when your professor announces exam schedules.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- -------------------------------------------------------------------------
     TAB 4: LEARNING MATERIALS & READINGS
     ------------------------------------------------------------------------- --}}
<div id="tab-materials" class="tab-pane" role="tabpanel">
    <div class="card" style="padding: var(--space-6);">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: 2px;">
                    Curriculum Readings & Lecture Documents
                </h2>
                <p style="font-size: var(--text-xs); color: var(--text-muted);">
                    Authorized slides, reference manuals, and lecture hand-outs uploaded by faculty
                </p>
            </div>
            <span class="badge badge-success">{{ $course->materials->count() }} Resources</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @forelse($course->materials as $mat)
                <div class="classroom-item-card">
                    <div class="flex items-center gap-4" style="flex: 1; min-width: 260px;">
                        <div class="classroom-item-icon icon-material">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                            </svg>
                        </div>
                        <div>
                            <div style="color: var(--ivory); font-weight: 600; font-size: var(--text-sm);">
                                {{ $mat->title }}
                            </div>
                            @if($mat->description)
                                <div style="font-size: var(--text-xs); color: var(--text-secondary); margin-top: 3px;">
                                    {{ $mat->description }}
                                </div>
                            @endif
                            <div style="font-size: 0.72rem; color: var(--text-muted); margin-top: 4px; display: flex; gap: var(--space-3);">
                                <span class="badge badge-neutral" style="font-size: 0.68rem; text-transform: uppercase;">{{ $mat->file_type ?? 'DOCUMENT' }}</span>
                                <span>Uploaded {{ $mat->created_at ? $mat->created_at->format('M d, Y') : 'This semester' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($mat->file_type === 'link' || filter_var($mat->file_path, FILTER_VALIDATE_URL))
                            <a href="{{ $mat->file_path }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                                Open Resource
                            </a>
                        @else
                            <a href="{{ route('materials.download', $mat) }}" class="btn btn-secondary btn-sm">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="academic-empty-state">
                    <div class="academic-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                        </svg>
                    </div>
                    <div class="academic-empty-title">No Lecture Materials Uploaded</div>
                    <div class="academic-empty-desc">
                        No supplementary lecture readings, slides, or syllabus files have been added yet. Your instructor will publish materials as topics progress.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- -------------------------------------------------------------------------
     TAB 5: FACULTY ANNOUNCEMENTS
     ------------------------------------------------------------------------- --}}
<div id="tab-announcements" class="tab-pane" role="tabpanel">
    <div class="card" style="padding: var(--space-6);">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: 2px;">
                    Official Classroom Notices & Announcements
                </h2>
                <p style="font-size: var(--text-xs); color: var(--text-muted);">
                    Direct alerts, schedule updates, and exam briefings from your instructor
                </p>
            </div>
            <span class="badge badge-warning">{{ $course->announcements->count() }} Notices</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @forelse($course->announcements as $notice)
                <div class="classroom-item-card" style="align-items: flex-start;">
                    <div class="flex items-start gap-4" style="flex: 1;">
                        <div class="classroom-item-icon icon-announcement" style="margin-top: 2px;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                                <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                            </svg>
                        </div>
                        <div style="flex: 1;">
                            <div class="flex justify-between items-center mb-1">
                                <h3 style="color: var(--ivory); font-weight: 600; font-size: var(--text-sm);">
                                    {{ $notice->title }}
                                </h3>
                                <span style="font-size: 0.72rem; color: var(--text-muted);">
                                    {{ $notice->created_at->format('M d, Y • h:i A') }}
                                </span>
                            </div>
                            <div style="font-size: var(--text-xs); color: var(--text-secondary); line-height: 1.6; margin-top: 4px;">
                                {!! nl2br(e($notice->content)) !!}
                            </div>
                            <div style="font-size: 0.7rem; color: var(--gold); margin-top: 6px;">
                                Posted by: {{ $notice->author ? $notice->author->name : 'Course Faculty' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="academic-empty-state">
                    <div class="academic-empty-icon">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                    </div>
                    <div class="academic-empty-title">No Classroom Announcements</div>
                    <div class="academic-empty-desc">
                        No official course bulletins have been issued at this time. Immediate notifications will be posted here as they become available.
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Tab Switching Script --}}
<script>
function switchTab(tabId) {
    document.querySelectorAll('.classroom-tab-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
        if (btn.getAttribute('data-tab') === tabId) {
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');
        }
    });

    document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('active');
        if (pane.id === tabId) {
            pane.classList.add('active');
        }
    });

    if (history.replaceState) {
        history.replaceState(null, null, '#' + tabId.replace('tab-', ''));
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.classroom-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.getAttribute('data-tab'));
        });
    });

    // Handle hash on load
    if (window.location.hash) {
        const hashTab = 'tab-' + window.location.hash.substring(1);
        if (document.getElementById(hashTab)) {
            switchTab(hashTab);
        }
    }
});
</script>
@endsection
