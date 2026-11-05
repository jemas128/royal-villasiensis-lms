<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480', // 20MB max
            'external_link' => 'nullable|url',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        if ($course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        $filePath = '';
        $fileType = 'link';
        $fileSize = 0;

        if ($request->hasFile('file')) {
            $uploaded = $request->file('file');
            $filePath = $uploaded->store('materials', 'public');
            $fileType = strtolower($uploaded->getClientOriginalExtension() ?: 'file');
            $fileSize = $uploaded->getSize();
        } elseif ($request->filled('external_link')) {
            $filePath = $request->external_link;
            $fileType = 'link';
        } else {
            return back()->with('error', 'Please provide either a document file or an external resource link.');
        }

        CourseMaterial::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
        ]);

        return back()->with('success', 'Course material uploaded successfully.');
    }

    public function destroy(CourseMaterial $material, Request $request): RedirectResponse
    {
        $teacher = $request->user();
        if ($material->course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            abort(403);
        }

        // Clean up physical file if stored locally
        if ($material->file_type !== 'link' && ! filter_var($material->file_path, FILTER_VALIDATE_URL)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
            if (file_exists(public_path('storage/' . $material->file_path))) {
                @unlink(public_path('storage/' . $material->file_path));
            }
        }

        $material->delete();

        return back()->with('success', 'Course material removed.');
    }
}
