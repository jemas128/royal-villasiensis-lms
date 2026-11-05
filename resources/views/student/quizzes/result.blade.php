@extends('layouts.app')

@section('title', 'Examination Results — ' . $attempt->quiz->title)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <a href="{{ route('student.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <a href="{{ route('student.quizzes.show', $attempt->quiz) }}">{{ Str::limit($attempt->quiz->title, 20) }}</a>
    <span class="separator">/</span>
    <span class="current">Results</span>
@endsection

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="mb-6">
        <div class="flex gap-2 items-center mb-1">
            <span class="badge badge-gold">{{ $attempt->quiz->course->code }}</span>
            <span style="font-size: var(--text-xs); color: var(--text-muted);">Completed {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y @ h:i A') : '' }}</span>
        </div>
        <h1 class="page-title">Assessment Evaluation Summary</h1>
        <p class="page-subtitle">{{ $attempt->quiz->title }}</p>
    </div>

    {{-- Score Hero Card --}}
    @php
        $pct = $attempt->total_points > 0 ? round(($attempt->score / $attempt->total_points) * 100, 1) : 0;
        $passed = $pct >= 70;
    @endphp
    <div class="card mb-8 text-center" style="background: var(--deep-charcoal); border: 2px solid {{ $passed ? 'var(--gold)' : 'var(--error)' }}; border-radius: var(--radius-lg); padding: var(--space-8);">
        <div style="font-size: var(--text-xs); color: {{ $passed ? 'var(--gold)' : 'var(--error)' }}; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 700; margin-bottom: var(--space-2);">
            {{ $passed ? 'Assessment Completed — Passing Grade' : 'Assessment Completed — Academic Review Needed' }}
        </div>

        <div style="font-family: var(--font-serif); font-size: 3.5rem; font-weight: 700; color: var(--ivory); line-height: 1;">
            {{ $attempt->score }} <span style="font-size: 1.5rem; color: var(--text-muted);">/ {{ $attempt->total_points }} pts</span>
        </div>

        <div style="font-size: var(--text-xl); font-weight: 600; color: {{ $passed ? 'var(--gold)' : 'var(--error)' }}; margin-top: var(--space-2);">
            {{ $pct }}% Score
        </div>

        <div style="margin-top: var(--space-6); display: flex; justify-content: center; gap: var(--space-4);">
            <a href="{{ route('student.quizzes.show', $attempt->quiz) }}" class="btn btn-secondary">
                Quiz Overview
            </a>
            <a href="{{ route('student.courses.show', $attempt->quiz->course) }}" class="btn btn-primary">
                Return to Course
            </a>
        </div>
    </div>

    {{-- Questions Breakdown --}}
    <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); margin-bottom: var(--space-4);">
        Item Response Review
    </h2>

    <div style="display: flex; flex-direction: column; gap: var(--space-4);">
        @foreach($attempt->quiz->questions as $idx => $q)
            @php
                $userAns = $attempt->answers[$q->id] ?? null;
                $isCorrect = $userAns !== null && (int)$userAns === (int)$q->correct_answer;
            @endphp
            <div class="card" style="background: var(--deep-charcoal); border: 1px solid {{ $isCorrect ? 'var(--border-dark)' : 'var(--error-border)' }}; border-radius: var(--radius-lg); padding: var(--space-5);">
                <div class="flex justify-between items-center mb-2">
                    <span style="font-size: var(--text-xs); font-weight: 600; color: var(--text-muted);">Item #{{ $idx + 1 }}</span>
                    @if($isCorrect)
                        <span class="badge badge-success">+{{ $q->points }} Points (Correct)</span>
                    @else
                        <span class="badge badge-error">0 Points (Incorrect)</span>
                    @endif
                </div>

                <p style="color: var(--ivory); font-size: var(--text-sm); font-weight: 500; margin-bottom: var(--space-3);">
                    {{ $q->question }}
                </p>

                <div style="display: flex; flex-direction: column; gap: var(--space-2);">
                    @foreach($q->options as $optIdx => $option)
                        @php
                            $wasSelected = $userAns !== null && (int)$userAns === $optIdx;
                            $isRight = (int)$q->correct_answer === $optIdx;
                        @endphp
                        <div style="padding: var(--space-2) var(--space-3); border-radius: var(--radius-sm); font-size: var(--text-xs); display: flex; justify-content: space-between; align-items: center; 
                            background: {{ $isRight ? 'var(--success-bg)' : ($wasSelected ? 'var(--error-bg)' : 'var(--charcoal)') }};
                            border: 1px solid {{ $isRight ? 'var(--success-border)' : ($wasSelected ? 'var(--error-border)' : 'var(--border-dark)') }};
                            color: {{ $isRight ? 'var(--success)' : ($wasSelected ? 'var(--error)' : 'var(--text-secondary)') }};">
                            <span>
                                <strong>{{ chr(65 + $optIdx) }}.</strong> {{ $option }}
                            </span>
                            <div>
                                @if($wasSelected && $isRight)
                                    <strong style="color: var(--success);">✓ Your Answer (Correct)</strong>
                                @elseif($wasSelected)
                                    <strong style="color: var(--error);">✗ Your Answer</strong>
                                @elseif($isRight)
                                    <strong style="color: var(--success);">✓ Correct Answer</strong>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
