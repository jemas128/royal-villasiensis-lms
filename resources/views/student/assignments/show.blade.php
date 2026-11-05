@extends('layouts.app')

@section('title', 'Assignment — ' . $assignment->title)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <a href="{{ route('student.assignments.index') }}">Assignments</a>
    <span class="separator">/</span>
    <span class="current">{{ Str::limit($assignment->title, 25) }}</span>
@endsection

@section('content')
<div style="max-width: 840px; margin: 0 auto;">
    <div class="mb-6">
        <div class="flex gap-2 items-center mb-1">
            <span class="badge badge-gold">{{ $assignment->course->code }}</span>
            <span style="font-size: var(--text-xs); color: {{ $assignment->due_date->isPast() ? 'var(--text-muted)' : '#C4973B' }};">
                Deadline: {{ $assignment->due_date->format('l, F d, Y @ h:i A') }}
            </span>
        </div>
        <h1 class="page-title">{{ $assignment->title }}</h1>
        <p class="page-subtitle">Max Score: <strong style="color: var(--gold);">{{ $assignment->max_score }} Points</strong></p>
    </div>

    {{-- Instructions Card --}}
    <div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600; margin-bottom: var(--space-3); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            Assignment Prompt & Guidelines
        </h2>
        <div style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.7; white-space: pre-line;">
            {{ $assignment->description ?? 'Follow verbal guidelines provided in class lectures.' }}
        </div>
    </div>

    @if($submission)
        {{-- Submission Details Card --}}
        <div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
            <div class="flex justify-between items-center mb-4 border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
                <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">Your Submission</h2>
                <div>
                    @if($submission->score !== null)
                        <span class="badge badge-success" style="font-size: var(--text-sm); font-weight: 700; padding: 4px 10px;">
                            Graded: {{ $submission->score }} / {{ $assignment->max_score }} Points
                        </span>
                    @else
                        <span class="badge badge-warning">Awaiting Faculty Review</span>
                    @endif
                </div>
            </div>

            <div style="font-size: var(--text-xs); color: var(--text-muted); margin-bottom: var(--space-4);">
                Submitted on: <strong>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y h:i A') : 'N/A' }}</strong>
                @if($submission->submitted_at && $submission->submitted_at->gt($assignment->due_date))
                    <span style="color: var(--error); margin-left: 6px;">(Late Submission)</span>
                @endif
            </div>

            @if($submission->content)
                <div style="margin-bottom: var(--space-4); background: var(--charcoal); padding: var(--space-4); border-radius: var(--radius-md); font-size: var(--text-sm); color: var(--ivory); white-space: pre-wrap; line-height: 1.6;">
                    {{ $submission->content }}
                </div>
            @endif

            @if($submission->file_path)
                <div style="margin-bottom: var(--space-4);">
                    <a href="{{ route('submissions.download', $submission) }}" class="btn btn-secondary btn-sm">
                        📎 Download Submitted Attachment
                    </a>
                </div>
            @endif

            @if($submission->feedback)
                <div style="margin-top: var(--space-4); padding: var(--space-4); background: var(--gold-glow); border-left: 3px solid var(--gold); border-radius: var(--radius-sm);">
                    <div style="font-size: var(--text-xs); color: var(--gold); font-weight: 600; text-transform: uppercase; margin-bottom: 2px;">Faculty Feedback:</div>
                    <div style="color: var(--ivory); font-size: var(--text-sm); line-height: 1.5;">{{ $submission->feedback }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Submit / Resubmit Form --}}
    <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <h2 style="font-size: var(--text-md); color: var(--ivory); font-weight: 600; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            {{ $submission ? 'Update / Resubmit Work' : 'Submit Assignment Deliverable' }}
        </h2>

        <form method="POST" action="{{ route('student.assignments.submit', $assignment) }}" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="content" class="form-label">Written Submission / Commentary</label>
                <textarea id="content" name="content" class="form-textarea" rows="6" placeholder="Type your answer, analysis report, or project repository links here...">{{ old('content', $submission->content ?? '') }}</textarea>
            </div>

            <div class="form-group">
                <label for="file" class="form-label">Attachment File (PDF, ZIP, DOC up to 20MB)</label>
                <input type="file" id="file" name="file" class="form-input">
                @if($submission && $submission->file_path)
                    <div class="form-hint">Uploading a new file will replace your previous attachment.</div>
                @endif
            </div>

            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('student.assignments.index') }}" class="btn btn-secondary">Back to Assignments</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    {{ $submission ? 'Re-upload Submission' : 'Turn In Assignment' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
