<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();
        $courseIds = $student->enrolledCourses()->pluck('courses.id');

        $assignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with(['course', 'submissions' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        return view('student.assignments.index', compact('assignments'));
    }

    public function show(Assignment $assignment, Request $request): View
    {
        $student = $request->user();

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $assignment->load('course');

        return view('student.assignments.show', compact('assignment', 'submission'));
    }

    public function submit(Request $request, Assignment $assignment): RedirectResponse
    {
        $student = $request->user();

        $validated = $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:20480', // 20MB
        ]);

        if (empty($validated['content']) && !$request->hasFile('file')) {
            return back()->with('error', 'Please provide either text content or an attachment file.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
        }

        Submission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'content' => $validated['content'] ?? null,
                'file_path' => $filePath ?? ($submission->file_path ?? null),
                'submitted_at' => Carbon::now(),
            ]
        );

        return back()->with('success', 'Assignment submitted successfully.');
    }
}
