<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\Semester;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalCourses = Course::count();
        $totalDepartments = Department::count();
        $totalEnrollments = Enrollment::where('status', 'enrolled')->count();
        $currentSemester = Semester::with('academicYear')->where('is_current', true)->first();

        $pendingUsers = User::with('department')->where('is_active', false)->latest()->get();
        $pendingCount = $pendingUsers->count();

        $recentUsers = User::latest()->take(5)->get();
        $recentCourses = Course::with(['department', 'teacher'])->latest()->take(5)->get();
        $recentAnnouncements = Announcement::with('author')->latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalStudents',
            'totalTeachers',
            'totalCourses',
            'totalDepartments',
            'totalEnrollments',
            'currentSemester',
            'pendingUsers',
            'pendingCount',
            'recentUsers',
            'recentCourses',
            'recentAnnouncements'
        ));
    }
}
