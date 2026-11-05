<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    /**
     * Helper to verify student enrollment in the course.
     */
    protected function isEnrolled($student, Quiz $quiz): bool
    {
        if ($student->isAdmin()) {
            return true;
        }

        return $student->enrolledCourses()->where('courses.id', $quiz->course_id)->exists();
    }

    public function index(Request $request): View
    {
        $student = $request->user();
        $courseIds = $student->enrolledCourses()->pluck('courses.id');

        $quizzes = Quiz::whereIn('course_id', $courseIds)
            ->where('is_published', true)
            ->with(['course', 'questions', 'attempts' => function ($q) use ($student) {
                $q->where('student_id', $student->id);
            }])
            ->latest()
            ->paginate(15);

        return view('student.quizzes.index', compact('quizzes'));
    }

    public function show(Quiz $quiz, Request $request): View|RedirectResponse
    {
        $student = $request->user();

        if (! $this->isEnrolled($student, $quiz)) {
            return redirect()->route('student.quizzes.index')->with('error', 'You are not enrolled in the course for this assessment.');
        }

        if (! $quiz->is_published && ! $student->isAdmin()) {
            return redirect()->route('student.quizzes.index')->with('error', 'This assessment is currently unavailable.');
        }

        $quiz->load(['course', 'questions']);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $completedCount = $attempts->whereNotNull('completed_at')->count();
        $hasActiveAttempt = $attempts->whereNull('completed_at')->isNotEmpty();

        // Check availability dates
        $now = Carbon::now();
        $isUpcoming = $quiz->available_from && $now->lt($quiz->available_from);
        $isExpired = $quiz->available_until && $now->gt($quiz->available_until);

        $hasQuestions = $quiz->questions->isNotEmpty();
        $canAttempt = ($completedCount < $quiz->max_attempts || $hasActiveAttempt) && ! $isUpcoming && ! $isExpired && $hasQuestions;

        return view('student.quizzes.show', compact('quiz', 'attempts', 'canAttempt', 'isUpcoming', 'isExpired', 'hasQuestions', 'hasActiveAttempt'));
    }

    public function take(Quiz $quiz, Request $request): View|RedirectResponse
    {
        $student = $request->user();

        if (! $this->isEnrolled($student, $quiz)) {
            return redirect()->route('student.quizzes.index')->with('error', 'You are not enrolled in this course.');
        }

        if (! $quiz->is_published && ! $student->isAdmin()) {
            return redirect()->route('student.quizzes.index')->with('error', 'This assessment is not currently active.');
        }

        $quiz->load(['course', 'questions']);

        if ($quiz->questions->isEmpty()) {
            return redirect()->route('student.quizzes.show', $quiz)->with('error', 'This assessment has no questions configured yet.');
        }

        $now = Carbon::now();
        if ($quiz->available_from && $now->lt($quiz->available_from)) {
            return redirect()->route('student.quizzes.show', $quiz)->with('error', 'This examination has not opened yet.');
        }
        if ($quiz->available_until && $now->gt($quiz->available_until)) {
            return redirect()->route('student.quizzes.show', $quiz)->with('error', 'The deadline for this examination has passed.');
        }

        // Find existing incomplete attempt or start a new one
        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->first();

        $completedCount = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereNotNull('completed_at')
            ->count();

        if (! $activeAttempt) {
            if ($completedCount >= $quiz->max_attempts) {
                return redirect()->route('student.quizzes.show', $quiz)->with('error', 'You have exhausted all allowed attempts for this quiz.');
            }

            $activeAttempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'started_at' => $now,
                'total_points' => (int) $quiz->questions->sum('points'),
            ]);
        }

        // Calculate accurate remaining seconds based on server start time
        $totalAllowedSeconds = $quiz->duration_minutes * 60;
        $elapsedSeconds = $now->diffInSeconds($activeAttempt->started_at, false);
        $remainingSeconds = max(0, $totalAllowedSeconds - abs($elapsedSeconds));

        // If time expired while away
        if ($remainingSeconds <= 0) {
            $activeAttempt->update([
                'completed_at' => $now,
                'score' => $activeAttempt->score ?? 0,
                'answers' => $activeAttempt->answers ?? [],
            ]);

            return redirect()->route('student.quizzes.result', $activeAttempt)->with('warning', 'Your allotted examination time has expired.');
        }

        return view('student.quizzes.take', compact('quiz', 'activeAttempt', 'remainingSeconds'));
    }

    public function submit(Request $request, Quiz $quiz): RedirectResponse
    {
        $student = $request->user();

        if (! $this->isEnrolled($student, $quiz)) {
            return redirect()->route('student.quizzes.index')->with('error', 'You are not enrolled in this course.');
        }

        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        // If no incomplete attempt found, check completed count
        if (! $activeAttempt) {
            $completedCount = QuizAttempt::where('quiz_id', $quiz->id)
                ->where('student_id', $student->id)
                ->whereNotNull('completed_at')
                ->count();

            if ($completedCount >= $quiz->max_attempts) {
                return redirect()->route('student.quizzes.show', $quiz)->with('error', 'Maximum examination attempts reached.');
            }

            $activeAttempt = new QuizAttempt([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'started_at' => Carbon::now()->subMinutes($quiz->duration_minutes),
            ]);
        }

        $submittedAnswers = $request->input('answers', []);
        $questions = $quiz->questions;

        $earnedScore = 0;
        $totalPossible = 0;

        foreach ($questions as $question) {
            $totalPossible += $question->points;
            $selectedAnswer = $submittedAnswers[$question->id] ?? null;

            if ($selectedAnswer !== null && (int) $selectedAnswer === (int) $question->correct_answer) {
                $earnedScore += $question->points;
            }
        }

        $activeAttempt->answers = $submittedAnswers;
        $activeAttempt->score = $earnedScore;
        $activeAttempt->total_points = $totalPossible;
        $activeAttempt->completed_at = Carbon::now();
        $activeAttempt->save();

        return redirect()->route('student.quizzes.result', $activeAttempt)->with('success', 'Examination completed and submitted successfully.');
    }

    public function result(QuizAttempt $attempt, Request $request): View|RedirectResponse
    {
        $student = $request->user();

        if ($attempt->student_id !== $student->id && ! $student->isAdmin()) {
            return redirect()->route('student.quizzes.index')->with('error', 'Access denied to this assessment evaluation.');
        }

        $attempt->load(['quiz.course', 'quiz.questions']);

        return view('student.quizzes.result', compact('attempt'));
    }
}
