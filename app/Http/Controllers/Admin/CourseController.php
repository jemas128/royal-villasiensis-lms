<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::with(['department', 'teacher', 'semester.academicYear'])
            ->withCount('enrollments')
            ->latest()
            ->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();

        return view('admin.courses.create', compact('departments', 'teachers', 'semesters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'nullable|exists:users,id',
            'semester_id' => 'required|exists:semesters,id',
            'max_students' => 'nullable|integer|min:1',
            'schedule' => 'nullable|string|max:100',
            'room' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Course::create($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->where('is_active', true)->orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();

        return view('admin.courses.edit', compact('course', 'departments', 'teachers', 'semesters'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:courses,code,'.$course->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'credits' => 'required|integer|min:1|max:10',
            'department_id' => 'required|exists:departments,id',
            'teacher_id' => 'nullable|exists:users,id',
            'semester_id' => 'required|exists:semesters,id',
            'max_students' => 'nullable|integer|min:1',
            'schedule' => 'nullable|string|max:100',
            'room' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $course->update($validated);

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        if ($course->enrollments()->exists()) {
            return back()->with('error', 'Cannot delete a course that has enrolled students.');
        }

        $course->delete();

        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
