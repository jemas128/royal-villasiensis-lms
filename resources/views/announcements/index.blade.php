@extends('layouts.app')

@section('title', 'Announcements')

@section('breadcrumb')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="separator">/</span>
    <span class="current">Announcements</span>
@endsection

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
        <div>
            <h1 class="page-title">University Bulletins & Notices</h1>
            <p class="page-subtitle">Official communications from university directors and faculty</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
            <button type="button" class="btn btn-primary" onclick="document.getElementById('post-announcement-modal').style.display='flex'">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Post New Bulletin
            </button>
        @endif
    </div>

    {{-- Announcements Feed --}}
    <div style="display: flex; flex-direction: column; gap: var(--space-4);">
        @forelse($announcements as $announcement)
            <div class="card" style="background: var(--deep-charcoal); border: 1px solid {{ $announcement->is_pinned ? 'var(--gold-border)' : 'var(--border-dark)' }}; border-radius: var(--radius-lg); padding: var(--space-6); position: relative;">
                @if($announcement->is_pinned)
                    <div style="position: absolute; top: var(--space-4); right: var(--space-4); display: flex; align-items: center; gap: 4px; font-size: var(--text-xs); color: var(--gold); background: var(--gold-glow); padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        PINNED NOTICE
                    </div>
                @endif

                <div style="margin-bottom: var(--space-2);">
                    <div style="font-size: var(--text-xs); color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">
                        @if($announcement->course)
                            <span style="color: var(--gold); font-weight: 600;">{{ $announcement->course->code }}</span>: {{ $announcement->course->name }}
                        @else
                            <span style="color: var(--ivory); font-weight: 600;">Campus-Wide Bulletin</span>
                        @endif
                        • Published by {{ $announcement->author->name }} ({{ $announcement->author->role }}) • {{ $announcement->created_at->format('M d, Y h:i A') }}
                    </div>
                    <h2 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif); font-weight: 600;">
                        {{ $announcement->title }}
                    </h2>
                </div>

                <div style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.6; white-space: pre-line; margin-top: var(--space-3);">
                    {{ $announcement->content }}
                </div>

                @if(auth()->user()->isAdmin() || auth()->id() === $announcement->author_id)
                    <div style="margin-top: var(--space-4); pt-2; border-top: 1px solid var(--border-dark); display: flex; justify-content: flex-end;">
                        <form method="POST" action="{{ route('announcements.destroy', $announcement) }}" onsubmit="return confirm('Remove this bulletin notice?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-ghost btn-sm" style="color: var(--error);">
                                Remove Notice
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="card text-center" style="background: var(--deep-charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); padding: var(--space-12);">
                <div style="color: var(--text-muted); font-size: var(--text-md); margin-bottom: var(--space-2);">No announcements published yet.</div>
                <div style="color: var(--text-dim); font-size: var(--text-sm);">Check back later for university-wide bulletins or class notices.</div>
            </div>
        @endforelse

        <div>
            {{ $announcements->links() }}
        </div>
    </div>
</div>

{{-- Post Modal --}}
@if(auth()->user()->isAdmin() || auth()->user()->isTeacher())
<div id="post-announcement-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 200; align-items: center; justify-content: center; padding: var(--space-4);">
    <div style="background: var(--charcoal); border: 1px solid var(--border-dark); border-radius: var(--radius-lg); width: 100%; max-width: 600px; padding: var(--space-6); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4); border-bottom: 1px solid var(--border-dark); padding-bottom: var(--space-2);">
            <h3 style="font-size: var(--text-lg); color: var(--ivory); font-family: var(--font-serif);">Publish Bulletin Notice</h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="document.getElementById('post-announcement-modal').style.display='none'">✕</button>
        </div>

        <form method="POST" action="{{ route('announcements.store') }}">
            @csrf

            <div class="form-group">
                <label for="title" class="form-label">Notice Headline <span class="required">*</span></label>
                <input type="text" id="title" name="title" class="form-input" required placeholder="e.g. Schedule Update for Fall Midterms">
            </div>

            @if(count($courses) > 0)
                <div class="form-group">
                    <label for="course_id" class="form-label">Audience Scope</label>
                    <select name="course_id" id="course_id" class="form-select">
                        @if(auth()->user()->isAdmin())
                            <option value="">Campus-Wide (All Scholars & Faculty)</option>
                        @endif
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->code }} — {{ $course->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="form-group">
                <label for="content" class="form-label">Bulletin Content <span class="required">*</span></label>
                <textarea id="content" name="content" class="form-textarea" rows="5" required placeholder="Provide complete notice details..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-checkbox-group">
                    <input type="checkbox" name="is_pinned" value="1" class="form-checkbox">
                    <span class="form-checkbox-label">Pin this bulletin to the top</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: var(--space-3); margin-top: var(--space-6);">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('post-announcement-modal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Publish Announcement</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
