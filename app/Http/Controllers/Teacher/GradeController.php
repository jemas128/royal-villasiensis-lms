<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $courses = Course::where('teacher_id', $teacher->id)->with('semester')->get();

        $selectedCourseId = $request->get('course_id', $courses->first()?->id);
        $selectedCourse = null;
        $roster = collect();

        if ($selectedCourseId) {
            $selectedCourse = Course::with(['students.grades' => function ($q) use ($selectedCourseId) {
                $q->where('course_id', $selectedCourseId);
            }, 'assignments.submissions', 'quizzes.attempts'])->find($selectedCourseId);

            if ($selectedCourse && ($selectedCourse->teacher_id === $teacher->id || $teacher->isAdmin())) {
                $roster = $selectedCourse->students;
            }
        }

        return view('teacher.grades.index', compact('courses', 'selectedCourse', 'roster'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:users,id',
            'grades.*.numeric_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.letter_grade' => 'nullable|string|max:5',
            'grades.*.remarks' => 'nullable|string|max:255',
        ]);

        $course = Course::findOrFail($request->course_id);
        if ($course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        foreach ($request->grades as $gradeData) {
            if ($gradeData['numeric_grade'] !== null || !empty($gradeData['letter_grade'])) {
                Grade::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'student_id' => $gradeData['student_id'],
                    ],
                    [
                        'numeric_grade' => $gradeData['numeric_grade'],
                        'letter_grade' => $gradeData['letter_grade'],
                        'remarks' => $gradeData['remarks'] ?? null,
                    ]
                );
            }
        }

        return back()->with('success', 'Official student grade roster recorded successfully.');
    }
}
