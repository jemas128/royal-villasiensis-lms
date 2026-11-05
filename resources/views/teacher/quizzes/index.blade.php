@extends('layouts.app')

@section('title', 'Manage Quizzes')

@section('breadcrumb')
    <a href="{{ route('teacher.dashboard') }}">Faculty</a>
    <span class="separator">/</span>
    <span class="current">Quizzes</span>
@endsection

@section('content')
<div class="mb-6 flex justify-between items-center" style="flex-wrap: wrap; gap: var(--space-4);">
    <div>
        <h1 class="page-title">Examination & Quiz Manager</h1>
        <p class="page-subtitle">Formative assessments, timed midterms, and automated question evaluation</p>
    </div>
    <a href="{{ route('teacher.quizzes.create') }}" class="btn btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
        Create New Quiz
    </a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Quiz Title</th>
                <th>Course</th>
                <th>Duration</th>
                <th>Questions</th>
                <th>Attempts</th>
                <th>Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quizzes as $quiz)
                <tr>
                    <td>
                        <strong style="color: var(--ivory); font-size: var(--text-md);">
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" style="color: var(--ivory);">{{ $quiz->title }}</a>
                        </strong>
                        <div style="font-size: var(--text-xs); color: var(--text-muted);">{{ Str::limit($quiz->description, 50) }}</div>
                    </td>
                    <td>
                        <span class="badge badge-gold">{{ $quiz->course->code }}</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $quiz->duration_minutes }} minutes
                    </td>
                    <td>
                        <span class="badge badge-neutral">{{ $quiz->questions_count }} items</span>
                    </td>
                    <td style="font-size: var(--text-sm); color: var(--text-secondary);">
                        {{ $quiz->attempts_count }} attempts
                    </td>
                    <td>
                        @if($quiz->is_published)
                            <span class="badge badge-success">Published</span>
                        @else
                            <span class="badge badge-neutral">Draft</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('teacher.quizzes.show', $quiz) }}" class="btn btn-primary btn-sm">Manage Questions</a>
                            <a href="{{ route('teacher.quizzes.edit', $quiz) }}" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="{{ route('teacher.quizzes.destroy', $quiz) }}" onsubmit="return confirm('Delete this quiz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: var(--space-8); color: var(--text-muted);">No quizzes created yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $quizzes->links() }}
</div>
@endsection
