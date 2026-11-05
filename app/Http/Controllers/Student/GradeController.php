<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $grades = Grade::where('student_id', $student->id)
            ->with(['course.teacher', 'course.semester.academicYear'])
            ->get();

        $gpa = $grades->whereNotNull('numeric_grade')->avg('numeric_grade');

        return view('student.grades.index', compact('grades', 'gpa'));
    }
}
