<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(Request $request): View
    {
        $teacher = $request->user();
        $courseIds = Course::where('teacher_id', $teacher->id)->pluck('id');

        $quizzes = Quiz::whereIn('course_id', $courseIds)
            ->with(['course'])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->paginate(15);

        return view('teacher.quizzes.index', compact('quizzes'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        $courses = $user->isAdmin()
            ? Course::orderBy('code')->get()
            : Course::where('teacher_id', $user->id)->orderBy('code')->get();

        return view('teacher.quizzes.create', compact('courses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user();

        $rules = [
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:360',
            'max_attempts' => 'required|integer|min:1|max:10',
            'available_from' => 'nullable|date',
            'available_until' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('available_from') && $value) {
                        if (strtotime($value) < strtotime($request->input('available_from'))) {
                            $fail('The available until date must be a date after or equal to available from.');
                        }
                    }
                },
            ],
        ];

        $validated = $request->validate($rules);

        $course = Course::findOrFail($validated['course_id']);
        if ($course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            return back()->with('error', 'You are not authorized to create an assessment for this course.')->withInput();
        }

        // Sanitize nullable dates to true null (prevents MySQL strict datetime format error on empty strings)
        $validated['available_from'] = $request->filled('available_from') ? $validated['available_from'] : null;
        $validated['available_until'] = $request->filled('available_until') ? $validated['available_until'] : null;
        $validated['is_published'] = $request->has('is_published');

        $quiz = Quiz::create($validated);

        return redirect()->route('teacher.quizzes.show', $quiz)->with('success', 'Quiz created successfully. You can now add examination questions.');
    }

    public function show(Quiz $quiz, Request $request): View
    {
        if ($quiz->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $quiz->load(['course', 'questions', 'attempts.student']);

        return view('teacher.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz, Request $request): View
    {
        $user = $request->user();
        if ($quiz->course->teacher_id !== $user->id && ! $user->isAdmin()) {
            abort(403);
        }

        $courses = $user->isAdmin()
            ? Course::orderBy('code')->get()
            : Course::where('teacher_id', $user->id)->orderBy('code')->get();

        return view('teacher.quizzes.edit', compact('quiz', 'courses'));
    }

    public function update(Request $request, Quiz $quiz): RedirectResponse
    {
        $teacher = $request->user();
        if ($quiz->course->teacher_id !== $teacher->id && ! $teacher->isAdmin()) {
            return back()->with('error', 'You are not authorized to edit this assessment.');
        }

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:360',
            'max_attempts' => 'required|integer|min:1|max:10',
            'available_from' => 'nullable|date',
            'available_until' => [
                'nullable',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->filled('available_from') && $value) {
                        if (strtotime($value) < strtotime($request->input('available_from'))) {
                            $fail('The available until date must be a date after or equal to available from.');
                        }
                    }
                },
            ],
        ];

        $validated = $request->validate($rules);

        $validated['available_from'] = $request->filled('available_from') ? $validated['available_from'] : null;
        $validated['available_until'] = $request->filled('available_until') ? $validated['available_until'] : null;
        $validated['is_published'] = $request->has('is_published');

        $quiz->update($validated);

        return redirect()->route('teacher.quizzes.show', $quiz)->with('success', 'Quiz settings updated.');
    }

    public function destroy(Quiz $quiz, Request $request): RedirectResponse
    {
        if ($quiz->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $quiz->delete();

        return redirect()->route('teacher.quizzes.index')->with('success', 'Quiz deleted successfully.');
    }

    public function storeQuestion(Request $request, Quiz $quiz): RedirectResponse
    {
        if ($quiz->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'question' => 'required|string',
            'options' => 'required|array',
            'correct_answer' => 'required|integer|min:0',
            'points' => 'required|integer|min:1',
        ]);

        // Filter non-empty options (supports 2, 3, or 4 options for True/False and Multiple Choice)
        $cleanOptions = array_values(array_filter($validated['options'], fn($opt) => !is_null($opt) && trim($opt) !== ''));

        if (count($cleanOptions) < 2) {
            return back()->with('error', 'Please provide at least 2 valid options for this question.');
        }

        $correctIndex = (int) $validated['correct_answer'];
        if ($correctIndex >= count($cleanOptions)) {
            $correctIndex = 0;
        }

        $quiz->questions()->create([
            'question' => $validated['question'],
            'options' => $cleanOptions,
            'correct_answer' => $correctIndex,
            'points' => (int) $validated['points'],
            'sort_order' => $quiz->questions()->count() + 1,
        ]);

        return back()->with('success', 'Question added to assessment.');
    }

    public function destroyQuestion(QuizQuestion $question, Request $request): RedirectResponse
    {
        if ($question->quiz->course->teacher_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        $question->delete();

        return back()->with('success', 'Question removed.');
    }
}
