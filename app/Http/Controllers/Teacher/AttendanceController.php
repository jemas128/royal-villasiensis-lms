<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $courses = Course::where('teacher_id', $teacher->id)->with('semester')->get();

        $selectedCourseId = $request->get('course_id', $courses->first()?->id);
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        $selectedCourse = null;
        $roster = collect();
        $attendances = collect();

        if ($selectedCourseId) {
            $selectedCourse = Course::with('students')->find($selectedCourseId);

            if ($selectedCourse && ($selectedCourse->teacher_id === $teacher->id || $teacher->isAdmin())) {
                $roster = $selectedCourse->students;
                $attendances = Attendance::where('course_id', $selectedCourseId)
                    ->where('date', $date)
                    ->get()
                    ->keyBy('student_id');
            }
        }

        return view('teacher.attendance.index', compact('courses', 'selectedCourse', 'date', 'roster', 'attendances'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.remarks' => 'nullable|string|max:255',
        ]);

        $course = Course::findOrFail($request->course_id);
        if ($course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        foreach ($request->attendances as $item) {
            Attendance::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'student_id' => $item['student_id'],
                    'date' => $request->date,
                ],
                [
                    'status' => $item['status'],
                    'remarks' => $item['remarks'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Class attendance recorded for '.Carbon::parse($request->date)->toFormattedDateString());
    }
}
