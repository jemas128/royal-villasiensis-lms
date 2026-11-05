<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();

        $courses = Course::where('teacher_id', $teacher->id)
            ->with(['semester', 'department'])
            ->withCount('students')
            ->get();

        $courseIds = $courses->pluck('id');

        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('status', 'enrolled')
            ->distinct('student_id')
            ->count('student_id');

        $activeAssignments = Assignment::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->count();

        $pendingGrading = Submission::whereIn('assignment_id', function ($q) use ($courseIds) {
                $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })
            ->whereNull('score')
            ->count();

        $recentSubmissions = Submission::whereIn('assignment_id', function ($q) use ($courseIds) {
                $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })
            ->with(['student', 'assignment.course'])
            ->latest()
            ->take(6)
            ->get();

        $activeQuizzes = Quiz::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->count();

        return view('dashboard.teacher', compact(
            'courses',
            'totalStudents',
            'activeAssignments',
            'pendingGrading',
            'recentSubmissions',
            'activeQuizzes'
        ));
    }
}
