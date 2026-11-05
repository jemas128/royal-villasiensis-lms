@extends('layouts.app')

@section('title', $course->code . ' — ' . $course->name)

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.courses.index') }}">Courses</a>
    <span class="separator">/</span>
    <span class="current">{{ $course->code }}</span>
@endsection

@section('content')
{{-- =========================================================================
     TEACHER COURSE HEADER
     ========================================================================= --}}
<div class="mb-6">
    <div class="card" style="background: radial-gradient(ellipse at 85% 15%, rgba(200, 164, 93, 0.12), transparent 60%), linear-gradient(180deg, #1f1e1c 0%, #161514 100%); border: 1px solid rgba(200, 164, 93, 0.25); border-radius: var(--radius-xl); padding: var(--space-6);">
        <div class="flex justify-between items-start" style="flex-wrap: wrap; gap: var(--space-4);">
            <div style="flex: 1; min-width: 280px;">
                <div class="flex gap-2 items-center mb-2">
                    <span class="badge badge-gold" style="font-size: var(--text-md); font-weight: 700;">{{ $course->code }}</span>
                    <span class="badge badge-neutral">{{ $course->department->code }}</span>
                    <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $course->semester->academicYear->name }} • {{ $course->semester->name }}</span>
                    <span class="badge badge-success">Instructor In Charge</span>
                </div>
                <h1 style="font-size: var(--text-2xl); color: var(--ivory); font-family: var(--font-serif); margin-bottom: var(--space-2);">
                    {{ $course->name }}
                </h1>
                <p style="font-size: var(--text-sm); color: var(--text-secondary); max-width: 800px; line-height: 1.6;">
                    {{ $course->description ?? 'No course syllabus description provided.' }}
                </p>
            </div>

            <div class="flex gap-2" style="flex-wrap: wrap;">
                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('upload-material-modal').style.display='flex'">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload Material
                </button>
                <a href="{{ route('teacher.assignments.create', ['course_id' => $course->id]) }}" class="btn btn-secondary btn-sm">
                    + New Assignment
                </a>
                <a href="{{ route('teacher.quizzes.create', ['course_id' => $course->id]) }}" class="btn btn-secondary btn-sm">
                    + New Quiz
                </a>
            </div>
        </div>

        <div style="margin-top: var(--space-5); border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: var(--space-4); display: flex; gap: var(--space-5); font-size: var(--text-xs); color: var(--text-muted); flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <strong>Schedule:</strong> {{ $course->schedule ?? 'TBA' }}
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 21h18M5 21V7l7-4 7 4v14"/></svg>
                <strong>Location:</strong> {{ $course->room ?? 'TBA' }}
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                <strong>Academic Units:</strong> {{ $course->credits }} Credits
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <strong>Class Size:</strong> {{ $course->students->count() }} scholars
            </div>
        </div>
    </div>
</div>

{{-- =========================================================================
     SUBJECT JOIN CODE SPOTLIGHT (FOR TEACHER TO SHARE WITH STUDENTS)
     ========================================================================= --}}
<div class="card mb-6" style="background: linear-gradient(135deg, rgba(200, 164, 93, 0.12), rgba(26, 26, 26, 0.95)); border: 1px solid var(--gold-border); border-radius: var(--radius-lg); padding: var(--space-5) var(--space-6);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge badge-gold" style="font-weight: 700;">SUBJECT JOIN CODE</span>
                <span style="font-size: var(--text-xs); color: var(--gold-light);">Required for student class entry</span>
            </div>
            <div style="font-size: var(--text-xs); color: var(--text-secondary); max-width: 520px; line-height: 1.5;">
                Share this enrollment code with students during lecture or orientation. When students submit this code, their request will be queued below for your approval.
            </div>
        </div>

        <div style="display: flex; align-items: center; gap: var(--space-3); flex-wrap: wrap;">
            <div class="enrollment-code-display" id="course-join-code">
                {{ $course->enrollment_code }}
            </div>

            <button type="button" class="btn btn-primary btn-sm" onclick="copyJoinCode('{{ $course->enrollment_code }}')" id="copy-btn">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="9" y="9" width="13" height="13" rx="2" ry="2"/>
                    <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                </svg>
                Copy Code
            </button>

            <form method="POST" action="{{ route('teacher.courses.regenerate-code', $course) }}" onsubmit="return confirm('Regenerate Enrollment Code: This will invalidate the existing code ({{ $course->enrollment_code }}). Continue?')">
                @csrf
                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--text-secondary);" title="Generate a fresh code">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="23 4 23 10 17 10"/>
                        <polyline points="1 20 1 14 7 14"/>
                        <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>
                    </svg>
                    Regenerate
                </button>
            </form>
        </div>
    </div>
</div>

{{-- =========================================================================
     PENDING STUDENT ADMISSION APPROVALS QUEUE
     ========================================================================= --}}
<div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid {{ $course->pendingEnrollments->count() > 0 ? 'var(--warning-border)' : 'var(--border-dark)' }}; border-radius: var(--radius-lg); padding: var(--space-6);">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-3">
            <div style="width: 34px; height: 34px; border-radius: var(--radius-md); background: {{ $course->pendingEnrollments->count() > 0 ? 'var(--warning-bg)' : 'rgba(255,255,255,0.05)' }}; color: {{ $course->pendingEnrollments->count() > 0 ? 'var(--warning)' : 'var(--text-muted)' }}; display: flex; align-items: center; justify-content: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600; margin-bottom: 2px;">
                    Pending Student Admission Requests ({{ $course->pendingEnrollments->count() }})
                </h2>
                <div style="font-size: var(--text-xs); color: var(--text-muted);">
                    Scholars who entered your class code and are awaiting your authorization to enter
                </div>
            </div>
        </div>
        @if($course->pendingEnrollments->count() > 0)
            <span class="badge badge-warning" style="animation: pulse 1.5s infinite;">
                Action Required
            </span>
        @endif
    </div>

    @if($course->pendingEnrollments->count() > 0)
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Scholar Applicant</th>
                        <th>Student ID</th>
                        <th>Department</th>
                        <th>Request Date</th>
                        <th style="text-align: right;">Teacher Decision</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($course->pendingEnrollments as $pending)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--gold-glow); border: 1px solid var(--gold); color: var(--gold); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: var(--text-xs);">
                                        {{ strtoupper(substr($pending->student->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="color: var(--ivory); font-weight: 600; font-size: var(--text-sm);">
                                            {{ $pending->student->name ?? 'Unknown Student' }}
                                        </div>
                                        <div style="font-size: var(--text-xs); color: var(--text-muted);">
                                            {{ $pending->student->email ?? '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-family: 'SF Mono', Consolas, monospace; color: var(--gold); font-size: var(--text-xs);">
                                    {{ $pending->student->student_id ?? 'STU-NEW' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: var(--text-xs); color: var(--text-secondary);">
                                    {{ $pending->student->department->name ?? 'Undergraduate' }}
                                </span>
                            </td>
                            <td>
                                <span style="font-size: var(--text-xs); color: var(--text-muted);">
                                    {{ $pending->created_at->format('M d, Y • h:i A') }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div class="flex gap-2 justify-end items-center">
                                    {{-- Approve Button --}}
                                    <form method="POST" action="{{ route('teacher.courses.enrollments.approve', ['course' => $course, 'enrollment' => $pending]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                            Approve & Admit
                                        </button>
                                    </form>

                                    {{-- Decline Button --}}
                                    <form method="POST" action="{{ route('teacher.courses.enrollments.reject', ['course' => $course, 'enrollment' => $pending]) }}" onsubmit="return confirm('Decline admission request for {{ $pending->student->name }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div style="text-align: center; padding: var(--space-6); color: var(--text-muted); font-size: var(--text-xs); background: rgba(0,0,0,0.2); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; gap: 6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            All student admission requests have been reviewed. Share code <strong>{{ $course->enrollment_code }}</strong> for new applicants to apply.
        </div>
    @endif
</div>

{{-- =========================================================================
     CLASSROOM MANAGEMENT SECTIONS (MATERIALS, ASSIGNMENTS, QUIZZES, ROSTER)
     ========================================================================= --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: var(--space-6);">
    {{-- Course Materials --}}
    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Syllabus & Lecture Materials ({{ $course->materials->count() }})</h2>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('upload-material-modal').style.display='flex'">+ Upload</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
            @forelse($course->materials as $mat)
                <div style="background: var(--charcoal); padding: var(--space-3); border-radius: var(--radius-md); border: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: var(--ivory); font-weight: 500; font-size: var(--text-sm);">{{ $mat->title }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">
                            {{ strtoupper($mat->file_type ?? 'DOCUMENT') }} • {{ $mat->description }}
                        </div>
                    </div>
                    <div class="flex gap-2 items-center">
                        @if($mat->file_type === 'link' || filter_var($mat->file_path, FILTER_VALIDATE_URL))
                            <a href="{{ $mat->file_path }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-sm">Open Link</a>
                        @else
                            <a href="{{ route('materials.download', $mat) }}" class="btn btn-secondary btn-sm">Download</a>
                        @endif
                        <form method="POST" action="{{ route('teacher.materials.destroy', $mat) }}" onsubmit="return confirm('Remove file?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); padding: var(--space-6); font-size: var(--text-sm);">
                    No materials uploaded yet.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Course Assignments --}}
    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Coursework & Assignments ({{ $course->assignments->count() }})</h2>
            <a href="{{ route('teacher.assignments.create', ['course_id' => $course->id]) }}" class="btn btn-ghost btn-sm">+ Add</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
            @forelse($course->assignments as $assign)
                <div style="background: var(--charcoal); padding: var(--space-3); border-radius: var(--radius-md); border: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: var(--ivory); font-weight: 500; font-size: var(--text-sm);">
                            <a href="{{ route('teacher.assignments.show', $assign) }}" style="color: var(--ivory);">{{ $assign->title }}</a>
                        </div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">
                            Due: {{ $assign->due_date ? $assign->due_date->format('M d, Y') : 'TBA' }} • Max: {{ $assign->max_score }} pts • {{ $assign->submissions->count() }} submitted
                        </div>
                    </div>
                    <a href="{{ route('teacher.assignments.show', $assign) }}" class="btn btn-secondary btn-sm">Submissions</a>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); padding: var(--space-6); font-size: var(--text-sm);">
                    No assignments posted.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Course Quizzes --}}
    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Online Assessments ({{ $course->quizzes->count() }})</h2>
            <a href="{{ route('teacher.quizzes.create', ['course_id' => $course->id]) }}" class="btn btn-ghost btn-sm">+ Add</a>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
            @forelse($course->quizzes as $quiz)
                <div style="background: var(--charcoal); padding: var(--space-3); border-radius: var(--radius-md); border: 1px solid var(--border-dark); display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div style="color: var(--ivory); font-weight: 500; font-size: var(--text-sm);">
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" style="color: var(--ivory);">{{ $quiz->title }}</a>
                        </div>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">
                            {{ $quiz->duration_minutes }} min • {{ $quiz->questions->count() }} questions • {{ $quiz->attempts->count() }} attempts
                        </div>
                    </div>
                    <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-secondary btn-sm">Manage</a>
                </div>
            @empty
                <div style="text-align: center; color: var(--text-muted); padding: var(--space-6); font-size: var(--text-sm);">
                    No quizzes created yet.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Enrolled Scholars Roster --}}
    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <div class="flex justify-between items-center mb-4">
            <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Enrolled Scholars ({{ $course->students->count() }})</h2>
            <a href="{{ route('teacher.attendance.index', ['course_id' => $course->id]) }}" class="btn btn-ghost btn-sm">Take Attendance</a>
        </div>

        <div style="max-height: 280px; overflow-y: auto;">
            <table style="font-size: var(--text-xs);">
                <thead>
                    <tr>
                        <th>Scholar</th>
                        <th>Student ID</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($course->students as $student)
                        <tr>
                            <td style="color: var(--ivory); font-weight: 500;">{{ $student->name }}</td>
                            <td style="color: var(--gold); font-family: monospace;">{{ $student->student_id ?? 'STU-NEW' }}</td>
                            <td style="text-align: right;">
                                @php
                                    $studentEnrollment = $student->enrollments->where('course_id', $course->id)->first();
                                @endphp
                                @if($studentEnrollment)
                                    <form method="POST" action="{{ route('teacher.courses.enrollments.remove', ['course' => $course, 'enrollment' => $studentEnrollment]) }}" onsubmit="return confirm('Remove {{ $student->name }} from this class?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error); padding: 2px 6px;">
                                            Remove
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="color: var(--text-muted);">No approved students enrolled yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Upload Material Modal --}}
<div id="upload-material-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 550px; padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Upload Lecture Material</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('upload-material-modal').style.display='none'" style="padding: 4px; line-height: 1;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" action="{{ route('teacher.materials.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="course_id" value="{{ $course->id }}">

            <div class="form-group">
                <label for="mat_title" class="form-label">Material Title <span class="required">*</span></label>
                <input type="text" id="mat_title" name="title" class="form-input" placeholder="e.g. Chapter 1: Introduction Slides" required>
            </div>

            <div class="form-group">
                <label for="mat_desc" class="form-label">Description / Instructions</label>
                <textarea id="mat_desc" name="description" class="form-textarea" rows="2" placeholder="Brief note for students..."></textarea>
            </div>

            <div class="form-group">
                <label for="mat_file" class="form-label">Document File (PDF, PPT, DOC, ZIP up to 20MB)</label>
                <input type="file" id="mat_file" name="file" class="form-input">
            </div>

            <div class="form-group">
                <label for="mat_link" class="form-label">Or External Resource URL</label>
                <input type="url" id="mat_link" name="external_link" class="form-input" placeholder="https://...">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('upload-material-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Upload to Course</button>
            </div>
        </form>
    </div>
</div>

<script>
function copyJoinCode(code) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(code).then(() => {
            showCopyFeedback();
        }).catch(() => fallbackCopy(code));
    } else {
        fallbackCopy(code);
    }
}

function fallbackCopy(text) {
    const tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
    showCopyFeedback();
}

function showCopyFeedback() {
    const btn = document.getElementById('copy-btn');
    if (btn) {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '✓ Copied!';
        btn.classList.add('btn-success');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
        }, 2000);
    }
}
</script>
@endsection
