<?php

namespace App\Http\Controllers;

use App\Models\CourseMaterial;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class MaterialDownloadController extends Controller
{
    /**
     * Download or redirect to a course lecture/learning material.
     */
    public function download(CourseMaterial $material, Request $request)
    {
        $user = $request->user();

        // Check user access: Admin, the course instructor, or an enrolled student
        $course = $material->course;
        if (! $user->isAdmin() && $course->teacher_id !== $user->id) {
            $isEnrolled = $course->enrollments()
                ->where('student_id', $user->id)
                ->where('status', 'enrolled')
                ->exists();

            if (! $isEnrolled) {
                return redirect()->route('dashboard')->with('error', 'You must be enrolled in this course to download learning materials.');
            }
        }

        // External Link handling
        if ($material->file_type === 'link' || filter_var($material->file_path, FILTER_VALIDATE_URL)) {
            return redirect()->away($material->file_path);
        }

        $filePath = $material->file_path;
        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: ($material->file_type ?: 'bin');
        $cleanTitle = Str::slug($material->title) ?: 'learning-material';
        $downloadName = $cleanTitle . '.' . $extension;

        // 1. Check Laravel Storage 'public' disk
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $downloadName);
        }

        // 2. Direct physical storage_path in app/public
        $fullPath = storage_path('app/public/' . $filePath);
        if (file_exists($fullPath)) {
            return response()->download($fullPath, $downloadName);
        }

        // 3. Direct public_path in storage/
        $publicPath = public_path('storage/' . $filePath);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $downloadName);
        }

        // 4. Fallback in storage/app/
        $appPath = storage_path('app/' . $filePath);
        if (file_exists($appPath)) {
            return response()->download($appPath, $downloadName);
        }

        return back()->with('error', 'The document file is not found on the server. Please contact your instructor.');
    }

    /**
     * Securely download a student's assignment submission attachment.
     */
    public function downloadSubmission(Submission $submission, Request $request)
    {
        $user = $request->user();
        $assignment = $submission->assignment;
        $course = $assignment->course;

        // Only the submitting student, the course instructor, or an admin can download
        if (! $user->isAdmin() && $submission->student_id !== $user->id && $course->teacher_id !== $user->id) {
            return redirect()->route('dashboard')->with('error', 'Access denied to this student submission.');
        }

        if (empty($submission->file_path)) {
            return back()->with('error', 'No attachment was provided for this submission.');
        }

        $filePath = $submission->file_path;
        $extension = pathinfo($filePath, PATHINFO_EXTENSION) ?: 'bin';
        $studentName = Str::slug($submission->student->name ?? 'student');
        $assignmentTitle = Str::slug($assignment->title ?? 'assignment');
        $downloadName = $studentName . '-' . $assignmentTitle . '.' . $extension;

        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $downloadName);
        }

        $fullPath = storage_path('app/public/' . $filePath);
        if (file_exists($fullPath)) {
            return response()->download($fullPath, $downloadName);
        }

        $publicPath = public_path('storage/' . $filePath);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $downloadName);
        }

        $appPath = storage_path('app/' . $filePath);
        if (file_exists($appPath)) {
            return response()->download($appPath, $downloadName);
        }

        return back()->with('error', 'The submission attachment file could not be found on the server.');
    }
}

