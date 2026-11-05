@extends('layouts.app')

@section('title', 'Examination In Progress — ' . $quiz->title)

@section('breadcrumb')
    <a href="{{ route('student.dashboard') }}">Student</a>
    <span class="separator">/</span>
    <a href="{{ route('student.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <span class="current">Examination In Progress</span>
@endsection

@section('content')
<div style="max-width: 820px; margin: 0 auto;">
    {{-- Sticky Assessment Header --}}
    <div class="card mb-6" style="background: var(--black); border: 1px solid var(--gold-border); border-radius: var(--radius-lg); padding: var(--space-4) var(--space-6); position: sticky; top: 68px; z-index: 50; display: flex; justify-content: space-between; align-items: center; box-shadow: var(--shadow-lg);">
        <div>
            <div style="font-size: var(--text-xs); color: var(--gold); text-transform: uppercase; font-weight: 600;">Active Examination</div>
            <div style="font-size: var(--text-md); color: var(--ivory); font-weight: 600;">{{ $quiz->title }}</div>
        </div>

        <div style="display: flex; align-items: center; gap: var(--space-3); background: var(--deep-charcoal); padding: var(--space-2) var(--space-4); border-radius: var(--radius-md); border: 1px solid var(--border-dark);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--gold);"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div>
                <span style="font-size: var(--text-xs); color: var(--text-muted);">Time Remaining:</span>
                @php
                    $initMins = floor(($remainingSeconds ?? ($quiz->duration_minutes * 60)) / 60);
                    $initSecs = ($remainingSeconds ?? ($quiz->duration_minutes * 60)) % 60;
                @endphp
                <span id="countdown-timer" style="font-family: monospace; font-size: var(--text-md); font-weight: 700; color: var(--gold); margin-left: 4px;">{{ sprintf('%02d:%02d', $initMins, $initSecs) }}</span>
            </div>
        </div>
    </div>

    <form id="quiz-form" method="POST" action="{{ route('student.quizzes.submit', $quiz) }}">
        @csrf

        <div style="display: flex; flex-direction: column; gap: var(--space-6);">
            @foreach($quiz->questions as $idx => $q)
                @php
                    $savedAns = $activeAttempt->answers[$q->id] ?? null;
                @endphp
                <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-6);">
                    <div class="flex justify-between items-center mb-3">
                        <span class="badge badge-gold" style="font-size: var(--text-xs); font-weight: 700;">Question {{ $idx + 1 }} of {{ $quiz->questions->count() }}</span>
                        <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $q->points }} Points</span>
                    </div>

                    <h2 style="font-size: var(--text-md); color: var(--ivory); line-height: 1.5; margin-bottom: var(--space-5); font-weight: 500;">
                        {{ $q->question }}
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                        @foreach($q->options as $optIdx => $option)
                            <label style="display: flex; align-items: center; gap: var(--space-4); padding: var(--space-3) var(--space-4); background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-md); cursor: pointer; transition: all var(--transition-fast);">
                                <input type="radio" name="answers[{{ $q->id }}]" value="{{ $optIdx }}" style="accent-color: var(--gold); width: 18px; height: 18px;" {{ $savedAns !== null && (int)$savedAns === $optIdx ? 'checked' : '' }}>
                                <span style="font-weight: 600; color: var(--gold); font-size: var(--text-sm);">{{ chr(65 + $optIdx) }}.</span>
                                <span style="color: var(--ivory); font-size: var(--text-sm);">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: var(--space-8); padding: var(--space-6); background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); display: flex; justify-content: space-between; align-items: center;">
            <div style="font-size: var(--text-xs); color: var(--text-muted);">
                Review all answers before submitting. Once submitted, your score will be computed instantly.
            </div>
            <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Submit assessment paper now? Answers cannot be changed.')">
                Submit Examination
            </button>
        </div>
    </form>
</div>

<script>
    (function() {
        var totalSeconds = {{ $remainingSeconds ?? ($quiz->duration_minutes * 60) }};
        var timerDisplay = document.getElementById('countdown-timer');

        function updateTimer() {
            var mins = Math.floor(totalSeconds / 60);
            var secs = totalSeconds % 60;
            timerDisplay.innerText = (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;

            if (totalSeconds < 120) {
                timerDisplay.style.color = 'var(--error)';
            }
        }

        updateTimer();

        var interval = setInterval(function() {
            totalSeconds--;
            if (totalSeconds <= 0) {
                clearInterval(interval);
                alert('Time expired! Your examination is being submitted automatically.');
                document.getElementById('quiz-form').submit();
                return;
            }
            updateTimer();
        }, 1000);
    })();
</script>
@endsection
