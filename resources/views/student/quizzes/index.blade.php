@extends('layouts.app')

@section('title', 'Examinations & Quizzes')

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <span class="current">Quizzes</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="page-title">Assessments & Quizzes</h1>
        <p class="page-subtitle">Test knowledge retention, complete term evaluations, and review attempts</p>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Quiz Title</th>
                <th>Course</th>
                <th>Duration</th>
                <th>Best Score</th>
                <th>Attempts Used</th>
                <th>Status</th>
                <th class="text-right">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quizzes as $quiz)
                @php
                    $myAttempts = $quiz->attempts;
                    $bestScore = $myAttempts->max('score');
                    $totalPts = $quiz->questions->sum('points') ?: $quiz->totalPoints();
                    $attemptsUsed = $myAttempts->whereNotNull('completed_at')->count();
                    $hasActive = $myAttempts->whereNull('completed_at')->isNotEmpty();
                    $now = \Carbon\Carbon::now();
                    $isExpired = $quiz->available_until && $now->gt($quiz->available_until);
                    $isUpcoming = $quiz->available_from && $now->lt($quiz->available_from);
                    $canTake = ($attemptsUsed < $quiz->max_attempts || $hasActive) && !$isExpired && !$isUpcoming && $quiz->questions->isNotEmpty();
                @endphp
                <tr>
                    <td>
                        <strong style="color: var(--ivory); font-size: var(--text-md);">
                            <a href="{{ route('student.quizzes.show', $quiz) }}" style="color: var(--ivory);">{{ $quiz->title }}</a>
                        </strong>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($quiz->description, 50) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-gold">{{ $quiz->course->code }}</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $quiz->duration_minutes }} min
                    </td>
                    <td>
                        @if($bestScore !== null)
                            <strong style="color: var(--gold);">{{ $bestScore }}</strong> / {{ $totalPts }} pts
                        @else
                            <span style="color: var(--text-dim);">Not attempted</span>
                        @endif
                    </td>
                    <td>
                        <span style="font-size: var(--text-sm); color: var(--text-secondary);">
                            {{ $attemptsUsed }} / {{ $quiz->max_attempts }}
                        </span>
                    </td>
                    <td>
                        @if($hasActive)
                            <span class="badge badge-warning">In Progress</span>
                        @elseif($isExpired)
                            <span class="badge badge-neutral">Closed</span>
                        @elseif($isUpcoming)
                            <span class="badge badge-info">Upcoming</span>
                        @elseif($attemptsUsed >= $quiz->max_attempts)
                            <span class="badge badge-neutral">Attempts Exhausted</span>
                        @elseif($attemptsUsed > 0)
                            <span class="badge badge-info">Retake Available</span>
                        @else
                            <span class="badge badge-success">Open</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('student.quizzes.show', $quiz) }}" class="btn {{ $canTake ? 'btn-primary' : 'btn-secondary' }} btn-sm">
                            {{ $hasActive ? 'Resume' : ($canTake ? 'Take Quiz' : 'View Overview') }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No quizzes available for your enrolled courses.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $quizzes->links() }}
</div>
@endsection
