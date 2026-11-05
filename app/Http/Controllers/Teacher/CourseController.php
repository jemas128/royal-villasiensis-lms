<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $courses = Course::where('teacher_id', $request->user()->id)
            ->with(['department', 'semester'])
            ->withCount(['students', 'pendingEnrollments', 'assignments', 'quizzes', 'materials'])
            ->get();

        return view('teacher.courses.index', compact('courses'));
    }

    public function show(Course $course, Request $request): View
    {
        if ($course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        // Ensure course has an enrollment code
        if (empty($course->enrollment_code)) {
            $course->update([
                'enrollment_code' => Course::generateEnrollmentCode($course->code),
            ]);
        }

        $course->load([
            'department',
            'semester.academicYear',
            'pendingEnrollments.student.department',
            'students.department',
            'assignments.submissions',
            'quizzes.attempts',
            'materials',
            'announcements.author'
        ]);

        return view('teacher.courses.show', compact('course'));
    }

    public function regenerateCode(Course $course, Request $request): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $newCode = Course::generateEnrollmentCode($course->code);
        $course->update(['enrollment_code' => $newCode]);

        return back()->with('success', "Enrollment code regenerated: {$newCode}. Share this with new scholars to join.");
    }

    public function approveEnrollment(Course $course, Enrollment $enrollment, Request $request): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $enrollment->update(['status' => Enrollment::STATUS_ENROLLED]);

        $studentName = $enrollment->student ? $enrollment->student->name : 'Student';

        return back()->with('success', "{$studentName} has been approved and enrolled in {$course->code}.");
    }

    public function rejectEnrollment(Course $course, Enrollment $enrollment, Request $request): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $studentName = $enrollment->student ? $enrollment->student->name : 'Applicant';
        $enrollment->delete();

        return back()->with('info', "Enrollment request for {$studentName} has been declined.");
    }

    public function removeStudent(Course $course, Enrollment $enrollment, Request $request): RedirectResponse
    {
        if ($course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($enrollment->course_id !== $course->id) {
            abort(404);
        }

        $studentName = $enrollment->student ? $enrollment->student->name : 'Student';
        $enrollment->delete();

        return back()->with('success', "{$studentName} has been removed from {$course->code}.");
    }
}
