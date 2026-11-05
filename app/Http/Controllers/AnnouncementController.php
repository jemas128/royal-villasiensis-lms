<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Announcement::with(['author', 'course'])->orderBy('is_pinned', 'desc')->latest();

        if ($user->isStudent()) {
            $enrolledCourseIds = $user->enrolledCourses()->pluck('courses.id');
            $query->where(function ($q) use ($enrolledCourseIds) {
                $q->whereNull('course_id')
                  ->orWhereIn('course_id', $enrolledCourseIds);
            });
        } elseif ($user->isTeacher()) {
            $taughtCourseIds = $user->taughtCourses()->pluck('id');
            $query->where(function ($q) use ($taughtCourseIds) {
                $q->whereNull('course_id')
                  ->orWhereIn('course_id', $taughtCourseIds);
            });
        }

        $announcements = $query->paginate(15);
        $courses = $user->isTeacher() ? $user->taughtCourses : ($user->isAdmin() ? Course::all() : collect());

        return view('announcements.index', compact('announcements', 'courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! $user->isTeacher()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
            'is_pinned' => 'boolean',
        ]);

        $validated['author_id'] = $user->id;
        $validated['is_pinned'] = $request->boolean('is_pinned');

        Announcement::create($validated);

        return redirect()->route('announcements.index')->with('success', 'Announcement published successfully.');
    }

    public function destroy(Announcement $announcement, Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && $announcement->author_id !== $user->id) {
            abort(403);
        }

        $announcement->delete();

        return redirect()->route('announcements.index')->with('success', 'Announcement removed.');
    }
}
