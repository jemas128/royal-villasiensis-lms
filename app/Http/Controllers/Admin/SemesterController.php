<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::with('academicYear')->withCount('courses')->latest()->paginate(15);
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.semesters.index', compact('semesters', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year' => 'nullable|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $academicYearName = trim($request->input('academic_year', ''));
        if (empty($academicYearName) && $request->filled('academic_year_id')) {
            $ay = AcademicYear::find($request->academic_year_id);
            $academicYearName = $ay?->name ?? '';
        }

        if (empty($academicYearName)) {
            return back()->withErrors(['academic_year' => 'Please enter the Academic Year.'])->withInput();
        }

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            Semester::query()->update(['is_current' => false]);
            AcademicYear::query()->update(['is_current' => false]);
        }

        $academicYear = AcademicYear::firstOrCreate(
            ['name' => $academicYearName],
            [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $isCurrent,
            ]
        );

        if ($isCurrent && !$academicYear->is_current) {
            $academicYear->update(['is_current' => true]);
        }

        Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $isCurrent,
        ]);

        return redirect()->route('admin.semesters.index')->with('success', 'Semester configured and added successfully.');
    }

    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year' => 'nullable|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        $academicYearName = trim($request->input('academic_year', ''));
        if (empty($academicYearName) && $request->filled('academic_year_id')) {
            $ay = AcademicYear::find($request->academic_year_id);
            $academicYearName = $ay?->name ?? '';
        }

        if (empty($academicYearName)) {
            $academicYear = $semester->academicYear;
        } else {
            $academicYear = AcademicYear::firstOrCreate(
                ['name' => $academicYearName],
                [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                    'is_current' => false,
                ]
            );
        }

        $isCurrent = $request->boolean('is_current');

        if ($isCurrent) {
            Semester::where('id', '!=', $semester->id)->update(['is_current' => false]);
        }

        $semester->update([
            'academic_year_id' => $academicYear->id,
            'name' => $validated['name'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_current' => $isCurrent,
        ]);

        return redirect()->route('admin.semesters.index')->with('success', 'Semester updated successfully.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        if ($semester->courses()->exists()) {
            return back()->with('error', 'Cannot delete a semester that has courses registered.');
        }

        $semester->delete();

        return redirect()->route('admin.semesters.index')->with('success', 'Semester removed successfully.');
    }
}
