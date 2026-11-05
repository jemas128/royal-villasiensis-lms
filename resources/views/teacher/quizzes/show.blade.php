@extends('layouts.app')

@section('title', 'Quiz Items & Attempts — ' . $quiz->title)

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <a href="{{ route('teacher.quizzes.index') }}">Quizzes</a>
    <span class="separator">/</span>
    <span class="current">{{ Str::limit($quiz->title, 25) }}</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-start" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <div class="flex gap-2 items-center mb-1">
            <span class="badge badge-gold">{{ $quiz->course->code }}</span>
            <span style="font-size: var(--text-xs); color: var(--text-muted);">{{ $quiz->duration_minutes }} minutes • Max {{ $quiz->max_attempts }} attempt(s)</span>
        </div>
        <h1 class="page-title">{{ $quiz->title }}</h1>
        <p class="page-subtitle">Total Questions: <strong style="color: var(--ivory);">{{ $quiz->questions->count() }}</strong> • Total Score: <strong style="color: var(--gold);">{{ $quiz->totalPoints() }} Points</strong></p>
    </div>
    <div class="flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('add-question-modal').style.display='flex'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Question Item
        </button>
        <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-secondary btn-sm">Edit Parameters</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: var(--space-6);">
    {{-- Questions List --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Assessment Items</h2>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('add-question-modal').style.display='flex'">+ Add Question</button>
        </div>

        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @forelse($quiz->questions as $index => $q)
                <div class="card" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-5);">
                    <div class="flex justify-between items-start mb-2">
                        <div style="font-size: var(--text-sm); font-weight: 600; color: var(--gold);">
                            Item #{{ $index + 1 }} <span style="font-size: var(--text-xs); color: var(--text-muted); font-weight: normal;">({{ $q->points }} pts)</span>
                        </div>
                        <form method="POST" action="{{ route('teacher.quizzes.questions.destroy', $q) }}" onsubmit="return confirm('Remove this question?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error); padding: 2px 6px;">✕</button>
                        </form>
                    </div>

                    <p style="color: var(--ivory); font-size: var(--text-sm); margin-bottom: var(--space-3); font-weight: 500;">
                        {{ $q->question }}
                    </p>

                    <div style="display: flex; flex-direction: column; gap: var(--space-2);">
                        @foreach($q->options as $optIdx => $option)
                            <div style="padding: var(--space-2) var(--space-3); border-radius: var(--radius-sm); font-size: var(--text-xs); display: flex; align-items: center; justify-content: space-between; background: {{ $optIdx === (int)$q->correct_answer ? 'var(--success-bg)' : 'var(--charcoal)' }}; border: 1px solid {{ $optIdx === (int)$q->correct_answer ? 'var(--success-border)' : 'var(--border-dark)' }}; color: {{ $optIdx === (int)$q->correct_answer ? 'var(--success)' : 'var(--text-secondary)' }};">
                                <span><strong>{{ chr(65 + $optIdx) }}.</strong> {{ $option }}</span>
                                @if($optIdx === (int)$q->correct_answer)
                                    <span style="font-size: 0.6875rem; font-weight: 700; color: var(--success); text-transform: uppercase;">Correct Answer</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="card text-center" style="padding: var(--space-8); background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); color: var(--text-muted);">
                    No questions created yet. Add questions to enable students to take this quiz.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Student Attempts Log --}}
    <div>
        <div class="flex justify-between items-center mb-3">
            <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Scholar Submissions ({{ $quiz->attempts->count() }})</h2>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Scholar</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Completed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quiz->attempts as $attempt)
                        <tr>
                            <td>
                                <div style="color: var(--ivory); font-weight: 500;">{{ $attempt->student->name }}</div>
                                <div style="font-size: var(--text-xs); color: var(--text-dim);">{{ $attempt->student->student_id }}</div>
                            </td>
                            <td>
                                <span class="badge badge-gold" style="font-weight: 700;">{{ $attempt->score }} / {{ $attempt->total_points }}</span>
                            </td>
                            <td style="font-size: var(--text-xs); color: var(--text-secondary);">
                                @php
                                    $pct = $attempt->total_points > 0 ? round(($attempt->score / $attempt->total_points) * 100, 1) : 0;
                                @endphp
                                <strong style="color: {{ $pct >= 75 ? 'var(--success)' : 'var(--error)' }};">{{ $pct }}%</strong>
                            </td>
                            <td style="font-size: var(--text-xs); color: var(--text-muted);">
                                {{ $attempt->completed_at ? $attempt->completed_at->format('M d, h:i A') : 'In Progress' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center" style="padding: var(--space-6); color: var(--text-muted);">No student attempts recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Add Question Modal --}}
<div id="add-question-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 650px; padding: var(--space-6); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Add Multiple-Choice Question</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('add-question-modal').style.display='none'">✕</button>
        </div>

        <form method="POST" action="{{ route('teacher.quizzes.questions.store', $quiz) }}">
            @csrf

            <div class="form-group">
                <label for="question_text" class="form-label">Question Prompt / Problem Statement <span class="required">*</span></label>
                <textarea id="question_text" name="question" class="form-textarea" rows="3" placeholder="Enter clear, concise question prompt..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Options (Mark the Radio of the Correct Answer) <span class="required">*</span></label>
                
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    @for($i = 0; $i < 4; $i++)
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <input type="radio" name="correct_answer" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }} style="accent-color: var(--gold); cursor: pointer;">
                            <span style="font-weight: 600; color: var(--gold); min-width: 20px;">{{ chr(65 + $i) }}</span>
                            <input type="text" name="options[]" class="form-input" placeholder="Option {{ chr(65 + $i) }} text {{ $i >= 2 ? '(optional)' : '' }}" {{ $i < 2 ? 'required' : '' }}>
                        </div>
                    @endfor
                </div>
            </div>

            <div class="form-group" style="max-width: 200px;">
                <label for="points_input" class="form-label">Points Worth <span class="required">*</span></label>
                <input type="number" id="points_input" name="points" value="5" min="1" class="form-input" required>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-question-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Question to Exam</button>
            </div>
        </form>
    </div>
</div>
@endsection
