<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

        $assignments = Assignment::whereIn('course_id', $courseIds)
            ->with(['course'])
            ->withCount(['submissions'])
            ->latest('due_date')
            ->paginate(15);

        return view('teacher.assignments.index', compact('assignments'));
    }

    public function create(Request $request): View
    {
        $courses = Course::where('teacher_id', $request->user()->id)->get();
        return view('teacher.assignments.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ($course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        $validated['is_published'] = $request->boolean('is_published', true);

        Assignment::create($validated);

        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment published successfully.');
    }

    public function show(Assignment $assignment, Request $request): View
    {
        if ($assignment->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $assignment->load(['course.students', 'submissions.student']);

        return view('teacher.assignments.show', compact('assignment'));
    }

    public function edit(Assignment $assignment, Request $request): View
    {
        if ($assignment->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $courses = Course::where('teacher_id', $request->user()->id)->get();
        return view('teacher.assignments.edit', compact('assignment', 'courses'));
    }

    public function update(Request $request, Assignment $assignment): RedirectResponse
    {
        if ($assignment->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $assignment->update($validated);

        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment updated successfully.');
    }

    public function destroy(Assignment $assignment, Request $request): RedirectResponse
    {
        if ($assignment->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $assignment->delete();

        return redirect()->route('teacher.assignments.index')->with('success', 'Assignment deleted.');
    }

    public function gradeSubmission(Request $request, Submission $submission): RedirectResponse
    {
        $teacher = $request->user();
        if ($submission->assignment->course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:'.$submission->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'graded_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Submission evaluated and graded successfully.');
    }
}
