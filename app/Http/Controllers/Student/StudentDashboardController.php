<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Quiz;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $enrolledCourses = $student->enrolledCourses()
            ->with(['teacher', 'semester'])
            ->get();

        $courseIds = $enrolledCourses->pluck('id');

        $upcomingAssignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->where('due_date', '>=', Carbon::now())
            ->whereDoesntHave('submissions', function ($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('course')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $availableQuizzes = Quiz::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', Carbon::now());
            })
            ->with('course')
            ->withCount(['attempts' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->get()
            ->filter(function ($quiz) {
                return $quiz->attempts_count < $quiz->max_attempts;
            })
            ->take(4);

        $grades = Grade::where('student_id', $student->id)
            ->with('course')
            ->get();

        $gpa = $grades->whereNotNull('numeric_grade')->avg('numeric_grade');

        $announcements = Announcement::where(function ($q) use ($courseIds) {
                $q->whereNull('course_id')
                  ->orWhereIn('course_id', $courseIds);
            })
            ->with('author')
            ->latest('is_pinned')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.student', compact(
            'enrolledCourses',
            'upcomingAssignments',
            'availableQuizzes',
            'grades',
            'gpa',
            'announcements'
        ));
    }
}
