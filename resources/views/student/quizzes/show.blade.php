@extends('layouts.app')

@section('title', 'Quiz — ' . $quiz->title)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <a href="{{ route('student.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <span class="current">{{ Str::limit($quiz->title, 25) }}</span>
@endsection

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="mb-6">
        <div class="flex gap-2 items-center mb-1">
            <span class="badge badge-gold">{{ $quiz->course->code }}</span>
            <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $quiz->course->name }}</span>
        </div>
        <h1 class="page-title">{{ $quiz->title }}</h1>
        <p class="page-subtitle">Examination & Evaluation Overview</p>
    </div>

    {{-- Parameters Card --}}
    <div class="card mb-6" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-6); text-align: center;">
            <div style="background: var(--charcoal); padding: var(--space-4); border-radius: var(--radius-md);">
                <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Time Limit</div>
                <div style="font-family: var(--font-serif); font-size: var(--text-2xl); font-weight: 700; color: var(--ivory); margin-top: 4px;">{{ $quiz->duration_minutes }} min</div>
            </div>
            <div style="background: var(--charcoal); padding: var(--space-4); border-radius: var(--radius-md);">
                <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Total Questions</div>
                <div style="font-family: var(--font-serif); font-size: var(--text-2xl); font-weight: 700; color: var(--ivory); margin-top: 4px;">{{ $quiz->questions->count() }} items</div>
            </div>
            <div style="background: var(--charcoal); padding: var(--space-4); border-radius: var(--radius-md);">
                <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Total Value</div>
                <div style="font-family: var(--font-serif); font-size: var(--text-2xl); font-weight: 700; color: var(--gold); margin-top: 4px;">{{ $quiz->totalPoints() }} pts</div>
            </div>
            <div style="background: var(--charcoal); padding: var(--space-4); border-radius: var(--radius-md);">
                <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase;">Attempts Allowed</div>
                <div style="font-family: var(--font-serif); font-size: var(--text-2xl); font-weight: 700; color: var(--ivory); margin-top: 4px;">{{ $quiz->max_attempts }} max</div>
            </div>
        </div>

        @if($quiz->description)
            <div style="margin-bottom: var(--space-6);">
                <div style="font-size: var(--text-xs); color: var(--text-dim); text-transform: uppercase; font-weight: 600; margin-bottom: var(--space-2);">Honor Guidelines & Description</div>
                <div style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.6; white-space: pre-line;">
                    {{ $quiz->description }}
                </div>
            </div>
        @endif

        <div style="display: flex; justify-content: center;">
            @if($canAttempt)
                <a href="{{ route('student.quizzes.take', $quiz) }}" class="btn btn-primary btn-lg" onclick="return confirm('{{ $hasActiveAttempt ? 'Resume examination in progress?' : 'Ready to start? The timer will begin once you open the examination paper.' }}')">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                    {{ $hasActiveAttempt ? 'Resume In-Progress Exam' : ($attempts->count() > 0 ? 'Start Retake Attempt' : 'Begin Assessment Now') }}
                </a>
            @elseif($isUpcoming)
                <div class="alert alert-info" style="margin-bottom: 0;">
                    This examination is scheduled to open on <strong>{{ $quiz->available_from->format('M d, Y @ h:i A') }}</strong>.
                </div>
            @elseif($isExpired)
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    The deadline for this examination passed on <strong>{{ $quiz->available_until->format('M d, Y @ h:i A') }}</strong>.
                </div>
            @elseif(!$hasQuestions)
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    The faculty instructor is currently preparing examination items for this quiz.
                </div>
            @else
                <div class="alert alert-warning" style="margin-bottom: 0;">
                    You have completed all allowed attempts ({{ $quiz->max_attempts }}) for this quiz.
                </div>
            @endif
        </div>
    </div>

    {{-- Previous Attempts Table --}}
    @if($attempts->count() > 0)
        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">Your Previous Examination Attempts</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Attempt #</th>
                        <th>Score Earned</th>
                        <th>Percentage</th>
                        <th>Completed Date</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attempts as $idx => $attempt)
                        @php
                            $pct = $attempt->total_points > 0 ? round(($attempt->score / $attempt->total_points) * 100, 1) : 0;
                        @endphp
                        <tr>
                            <td style="color: var(--ivory); font-weight: 500;">
                                Attempt {{ $attempts->count() - $idx }}
                            </td>
                            <td>
                                <strong style="color: var(--gold);">{{ $attempt->score }}</strong> / {{ $attempt->total_points }} pts
                            </td>
                            <td>
                                <span class="badge {{ $pct >= 75 ? 'badge-success' : 'badge-error' }}">
                                    {{ $pct }}%
                                </span>
                            </td>
                            <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                                {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y h:i A') : 'Incomplete' }}
                            </td>
                            <td class="text-right">
                                <a href="{{ route('student.quizzes.result', $attempt) }}" class="btn btn-secondary btn-sm">
                                    Review Paper
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
