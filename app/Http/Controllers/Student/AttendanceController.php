<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $attendances = Attendance::where('student_id', $student->id)
            ->with('course')
            ->orderBy('date', 'desc')
            ->paginate(20);

        $totalPresent = Attendance::where('student_id', $student->id)->where('status', 'present')->count();
        $totalAbsent = Attendance::where('student_id', $student->id)->where('status', 'absent')->count();
        $totalLate = Attendance::where('student_id', $student->id)->where('status', 'late')->count();
        $totalDays = Attendance::where('student_id', $student->id)->count();

        $attendanceRate = $totalDays > 0 ? round(($totalPresent / $totalDays) * 100, 1) : 100;

        return view('student.attendance.index', compact('attendances', 'totalPresent', 'totalAbsent', 'totalLate', 'attendanceRate'));
    }
}
