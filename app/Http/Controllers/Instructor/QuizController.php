<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QuizController extends Controller
{
    public function store(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'module_id' => 'nullable|exists:modules,id',
            'type' => 'required|in:quiz,exam,practice,survey',
            'passing_score' => 'required|integer|min:1|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
            'questions_per_attempt' => 'nullable|integer|min:1',
        ]);

        $validated['course_id'] = $course->id;
        $validated['is_active'] = true;
        $validated['is_published'] = false;
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');

        $quiz = Quiz::create($validated);

        return redirect()->route('instructor.quiz.edit', [$course->id, $quiz->id])
            ->with('success', 'Quiz créé avec succès. Ajoutez maintenant des questions.');
    }

    public function edit(int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = $course->quizzes()->with('questions.answers')->findOrFail($quizId);
        
        $modules = $course->modules;

        return view('instructor.quizzes.edit', compact('course', 'quiz', 'modules'));
    }

    public function update(Request $request, int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = $course->quizzes()->findOrFail($quizId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'module_id' => 'nullable|exists:modules,id',
            'type' => 'required|in:quiz,exam,practice,survey',
            'passing_score' => 'required|numeric|min:1|max:100',
            'duration_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean',
            'shuffle_questions' => 'nullable|boolean',
            'shuffle_answers' => 'nullable|boolean',
            'questions_per_attempt' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
        ]);

        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['shuffle_questions'] = $request->has('shuffle_questions');
        $validated['shuffle_answers'] = $request->has('shuffle_answers');
        $validated['is_published'] = $request->has('is_published');

        $quiz->update($validated);

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Quiz mis à jour avec succès.');
    }

    public function duplicate(int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = $course->quizzes()->with('questions.answers')->findOrFail($quizId);

        $newQuiz = $quiz->replicate();
        $newQuiz->title = $quiz->title . ' - Copie';
        $newQuiz->save();

        foreach ($quiz->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->quiz_id = $newQuiz->id;
            $newQuestion->save();

            foreach ($question->answers as $answer) {
                $newAnswer = $answer->replicate();
                $newAnswer->question_id = $newQuestion->id;
                $newAnswer->save();
            }
        }

        return back()->with('success', 'Quiz dupliqué avec succès.');
    }

    public function results(int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = $course->quizzes()->findOrFail($quizId);
        
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        return view('instructor.quizzes.results', compact('course', 'quiz', 'attempts'));
    }

    public function destroy(int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = $course->quizzes()->findOrFail($quizId);
        $quiz->delete();

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Quiz supprimé avec succès.');
    }
}
