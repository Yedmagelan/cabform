<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\QuizService;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected QuizService $quizService,
        protected CertificateService $certificateService,
    ) {}

    /**
     * Afficher le quiz.
     */
    public function show(string $slug, int $quizId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $quiz = Quiz::findOrFail($quizId);

        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->firstOrFail();

        // Banque de questions : Récupérer, mélanger et limiter les questions si configuré
        $questionsQuery = $quiz->questions()->with('answers');
        if ($quiz->shuffle_questions) {
            $questions = $questionsQuery->inRandomOrder()->get();
        } else {
            $questions = $questionsQuery->get();
        }

        if ($quiz->questions_per_attempt) {
            $questions = $questions->take($quiz->questions_per_attempt);
        }

        // Sauvegarder les questions présentées dans la session
        session(['quiz_questions_' . $quiz->id => $questions->pluck('id')->toArray()]);

        // Mélanger les réponses
        if ($quiz->shuffle_answers) {
            foreach ($questions as $question) {
                $question->setRelation('answers', $question->answers->shuffle());
            }
        }

        $quiz->setRelation('questions', $questions);

        $previousAttempts = auth()->user()->quizAttempts()
            ->where('quiz_id', $quiz->id)
            ->orderByDesc('created_at')
            ->get();

        return view('learner.course.quiz', compact('course', 'quiz', 'enrollment', 'previousAttempts'));
    }

    /**
     * Soumettre un quiz.
     */
    public function submit(Request $request, string $slug, int $quizId)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|integer',
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();
        $quiz = Quiz::findOrFail($quizId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Vérifier le nombre max de tentatives
        if ($quiz->max_attempts) {
            $attemptsCount = auth()->user()->quizAttempts()
                ->where('quiz_id', $quiz->id)
                ->count();

            if ($attemptsCount >= $quiz->max_attempts) {
                return back()->with('error', 'Nombre maximum de tentatives atteint.');
            }
        }

        // Récupérer les questions présentées depuis la session
        $questionIds = session('quiz_questions_' . $quizId);

        $attempt = $this->quizService->submit(
            auth()->user(),
            $quiz,
            $request->answers,
            $enrollment->id,
            $questionIds
        );

        // Si c'est un examen final et que l'utilisateur a réussi -> Certificat
        if ($attempt->passed && $quiz->type === 'exam' && $course->is_certified) {
            $this->certificateService->generate(auth()->user(), $course, $enrollment, $attempt->score);
            return redirect()->route('learner.quiz.result', [$slug, $quizId, $attempt->id])
                ->with('success', 'Félicitations ! Vous avez réussi et votre certificat a été généré !');
        }

        return redirect()->route('learner.quiz.result', [$slug, $quizId, $attempt->id]);
    }

    /**
     * Afficher le résultat d'un quiz.
     */
    public function result(string $slug, int $quizId, int $attemptId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $quiz = Quiz::findOrFail($quizId);
        $attempt = auth()->user()->quizAttempts()->findOrFail($attemptId);

        return view('learner.course.quiz-result', compact('course', 'quiz', 'attempt'));
    }
}
