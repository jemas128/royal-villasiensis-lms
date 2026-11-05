<?php

namespace App\Http\Controllers\Student;

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
        $student = $request->user();

        $enrolledCourses = $student->enrolledCourses()
            ->with(['teacher', 'department', 'semester'])
            ->withCount(['assignments', 'quizzes', 'materials'])
            ->get();

        $pendingEnrollments = Enrollment::where('student_id', $student->id)
            ->where('status', Enrollment::STATUS_PENDING)
            ->with(['course.teacher', 'course.department', 'course.semester'])
            ->latest()
            ->get();

        $enrolledIds = $enrolledCourses->pluck('id');
        $pendingCourseIds = $pendingEnrollments->pluck('course_id');

        $availableCourses = Course::whereNotIn('id', $enrolledIds->merge($pendingCourseIds))
            ->where('is_active', true)
            ->with(['teacher', 'department', 'semester'])
            ->orderBy('code')
            ->get();

        return view('student.courses.index', compact('enrolledCourses', 'pendingEnrollments', 'availableCourses'));
    }

    public function joinWithCode(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ], [
            'code.required' => 'Please enter the subject enrollment code provided by your teacher.',
        ]);

        $student = $request->user();
        $inputCode = strtoupper(trim($request->input('code')));

        $course = Course::whereRaw('UPPER(enrollment_code) = ?', [$inputCode])->first();

        if (! $course) {
            return back()->with('error', 'Invalid subject enrollment code. Please verify the code with your teacher.')->withInput();
        }

        if (! $course->is_active) {
            return back()->with('error', 'This course is currently not active for enrollment.')->withInput();
        }

        $existing = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            if ($existing->status === Enrollment::STATUS_ENROLLED) {
                return redirect()->route('student.courses.show', $course)->with('info', "You are already enrolled in {$course->code}: {$course->name}.");
            }

            if ($existing->status === Enrollment::STATUS_PENDING) {
                return back()->with('info', "You have already submitted a join request for {$course->code}. Please wait for your instructor to review and approve it.")->withInput();
            }

            // If previously dropped or rejected, allow re-requesting
            $existing->update(['status' => Enrollment::STATUS_PENDING]);
        } else {
            Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'status' => Enrollment::STATUS_PENDING,
            ]);
        }

        $teacherName = $course->teacher ? $course->teacher->name : 'Your teacher';

        return redirect()->route('student.courses.index')->with(
            'success',
            "Join request for {$course->code} ({$course->name}) submitted! {$teacherName} has been notified and must approve your class entry."
        );
    }

    public function cancelRequest(Enrollment $enrollment, Request $request): RedirectResponse
    {
        $student = $request->user();

        if ($enrollment->student_id !== $student->id && ! $student->isAdmin()) {
            abort(403);
        }

        if ($enrollment->status !== Enrollment::STATUS_PENDING) {
            return back()->with('error', 'Only pending course requests can be canceled.');
        }

        $courseCode = $enrollment->course->code ?? 'Course';
        $enrollment->delete();

        return redirect()->route('student.courses.index')->with('success', "Enrollment request for {$courseCode} has been canceled.");
    }

    public function show(Course $course, Request $request): View|RedirectResponse
    {
        $student = $request->user();

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (! $student->isAdmin()) {
            if (! $enrollment) {
                return redirect()->route('student.courses.index')->with(
                    'error',
                    "You are not enrolled in {$course->code}. Please enter the subject code provided by your instructor to join."
                );
            }

            if ($enrollment->status === Enrollment::STATUS_PENDING) {
                return redirect()->route('student.courses.index')->with(
                    'info',
                    "Your enrollment request for {$course->code} ({$course->name}) is pending teacher approval. You will gain access once your instructor approves."
                );
            }

            if ($enrollment->status !== Enrollment::STATUS_ENROLLED) {
                return redirect()->route('student.courses.index')->with(
                    'error',
                    "You do not have active enrollment status in {$course->code}."
                );
            }
        }

        $course->load([
            'teacher.department',
            'department',
            'semester.academicYear',
            'materials',
            'assignments' => function ($q) {
                $q->where('is_published', true)->orderBy('due_date');
            },
            'assignments.submissions' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            },
            'quizzes' => function ($q) {
                $q->where('is_published', true);
            },
            'quizzes.attempts' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            },
            'announcements.author',
            'students'
        ]);

        return view('student.courses.show', compact('course', 'enrollment'));
    }

    public function enroll(Course $course, Request $request): RedirectResponse
    {
        return redirect()->route('student.courses.index')->with(
            'error',
            'Self-enrollment is disabled. Please enter your teacher-provided subject code to request class entry.'
        );
    }

    public function drop(Course $course, Request $request): RedirectResponse
    {
        $student = $request->user();

        $enrollment = Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->first();
        if ($enrollment) {
            $enrollment->update(['status' => Enrollment::STATUS_DROPPED]);
        }

        return redirect()->route('student.courses.index')->with('success', 'Dropped from course '.$course->code);
    }
}
