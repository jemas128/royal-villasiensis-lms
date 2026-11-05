@extends('layouts.app')

@section('title', 'Assignment Submissions — ' . $assignment->title)

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.assignments.index') }}">Assignments</a>
    <span class="separator">/</span>
    <span class="current">{{ Str::limit($assignment->title, 25) }}</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-start" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <div class="flex gap-2 items-center mb-1">
            <span class="badge badge-gold">{{ $assignment->course->code }}</span>
            <span style="font-size: var(--text-xs); color: var(--text-muted);">Due: {{ $assignment->due_date->format('M d, Y h:i A') }}</span>
        </div>
        <h1 class="page-title">{{ $assignment->title }}</h1>
        <p class="page-subtitle">Max Score: <strong style="color: var(--gold);">{{ $assignment->max_score }} Points</strong></p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('teacher.assignments.edit', $assignment) }}" class="btn btn-secondary btn-sm">Edit Assignment</a>
    </div>
</div>

<div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
    <div style="font-size: var(--text-xs); color: var(--text-dim); text-transform: uppercase; font-weight: 600; margin-bottom: var(--space-2);">Assignment Instructions</div>
    <div style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.6; white-space: pre-line;">
        {{ $assignment->description ?? 'No specific instructions provided.' }}
    </div>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">Student Submissions & Evaluation</h2>
        <span style="font-size: var(--text-xs); color: var(--text-muted);">
            {{ $assignment->submissions->count() }} of {{ $assignment->course->students->count() }} scholars submitted
        </span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Scholar Name</th>
                <th>Student ID</th>
                <th>Submission Time</th>
                <th>Deliverable Content</th>
                <th>Score</th>
                <th class="text-right">Evaluate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assignment->course->students as $student)
                @php
                    $submission = $assignment->submissions->firstWhere('student_id', $student->id);
                @endphp
                <tr>
                    <td>
                        <div style="color: var(--ivory); font-weight: 500;">{{ $student->name }}</div>
                        <div style="font-size: var(--text-xs); color: var(--text-dim);">{{ $student->email }}</div>
                    </td>
                    <td style="font-size: var(--text-sm); font-family: monospace; color: var(--gold);">
                        {{ $student->student_id ?? '—' }}
                    </td>
                    <td>
                        @if($submission && $submission->submitted_at)
                            <div style="font-size: var(--text-xs); color: {{ $submission->submitted_at->gt($assignment->due_date) ? 'var(--error)' : 'var(--success)' }};">
                                {{ $submission->submitted_at->format('M d, h:i A') }}
                                @if($submission->submitted_at->gt($assignment->due_date))
                                    (Late)
                                @endif
                            </div>
                        @else
                            <span class="badge badge-neutral">Missing</span>
                        @endif
                    </td>
                    <td>
                        @if($submission)
                            <div style="font-size: var(--text-xs); color: var(--text-secondary); max-width: 250px;">
                                {{ Str::limit($submission->content, 60) }}
                            </div>
                            @if($submission->file_path)
                                <a href="{{ route('submissions.download', $submission) }}" class="btn btn-ghost btn-sm" style="padding: 2px 6px; font-size: 0.6875rem; color: var(--gold);">
                                    📎 Download Attachment
                                </a>
                            @endif
                        @else
                            <span style="font-size: var(--text-xs); color: var(--text-dim);">Not submitted</span>
                        @endif
                    </td>
                    <td>
                        @if($submission && $submission->score !== null)
                            <span class="badge badge-success" style="font-weight: 700;">{{ $submission->score }} / {{ $assignment->max_score }}</span>
                        @elseif($submission)
                            <span class="badge badge-warning">Needs Grading</span>
                        @else
                            <span style="color: var(--text-dim); font-size: var(--text-xs);">—</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @if($submission)
                            <button type="button" class="btn btn-primary btn-sm" onclick="openGradeModal({{ $submission->id }}, '{{ addslashes($student->name) }}', '{{ $submission->score ?? '' }}', '{{ addslashes($submission->feedback ?? '') }}', '{{ addslashes($submission->content ?? '') }}')">
                                {{ $submission->score !== null ? 'Edit Grade' : 'Grade' }}
                            </button>
                        @else
                            <span style="font-size: var(--text-xs); color: var(--text-dim);">No submission</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No students enrolled in this course.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Grading Modal --}}
<div id="grade-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 580px; padding: var(--space-6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);" id="modal-student-name">Grade Submission</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('grade-modal').style.display='none'">✕</button>
        </div>

        <form id="grade-form" method="POST">
            @csrf
            
            <div style="margin-bottom: var(--space-4); background: var(--deep-charcoal); padding: var(--space-3); border-radius: var(--radius-md); font-size: var(--text-xs); color: var(--text-secondary); max-height: 120px; overflow-y: auto;">
                <div style="color: var(--text-muted); margin-bottom: 2px;">Student Text Submission:</div>
                <div id="modal-submission-content" style="white-space: pre-wrap;"></div>
            </div>

            <div class="form-group">
                <label for="score_input" class="form-label">Score (out of {{ $assignment->max_score }}) <span class="required">*</span></label>
                <input type="number" id="score_input" name="score" min="0" max="{{ $assignment->max_score }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="feedback_input" class="form-label">Instructor Feedback & Academic Critique</label>
                <textarea id="feedback_input" name="feedback" class="form-textarea" rows="3" placeholder="Commend strengths, point out flaws, suggest further literature..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('grade-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Grade & Feedback</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openGradeModal(submissionId, studentName, score, feedback, content) {
        document.getElementById('modal-student-name').innerText = 'Grade: ' + studentName;
        document.getElementById('modal-submission-content').innerText = content || '(No text content submitted)';
        document.getElementById('score_input').value = score;
        document.getElementById('feedback_input').value = feedback;
        document.getElementById('grade-form').action = '/teacher/submissions/' + submissionId + '/grade';
        document.getElementById('grade-modal').style.display = 'flex';
    }
</script>
@endsection
