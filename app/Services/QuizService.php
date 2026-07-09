<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;

class QuizService
{
    /**
     * Soumettre et corriger automatiquement un quiz.
     */
    public function submit(User $user, Quiz $quiz, array $answers, int $enrollmentId, ?array $questionIds = null): QuizAttempt
    {
        if (!$questionIds) {
            $questionIds = $quiz->questions()->pluck('id')->toArray();
        }

        $totalQuestions = count($questionIds);
        $correctAnswers = 0;

        if ($totalQuestions > 0) {
            $questions = \App\Models\Question::whereIn('id', $questionIds)->with('answers')->get();
            foreach ($questions as $question) {
                $submittedAnswerId = $answers[$question->id] ?? null;
                $correctAnswer = $question->answers()->where('is_correct', true)->first();

                if ($submittedAnswerId && $correctAnswer && (int)$submittedAnswerId === $correctAnswer->id) {
                    $correctAnswers++;
                }
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $passed = $score >= $quiz->passing_score;

        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => $user->id,
            'enrollment_id' => $enrollmentId,
            'answers_data' => $answers, // in the migration, the column name is answers_data! (not answers)
            'score' => $score,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'passed' => $passed,
            'started_at' => now()->subMinutes(rand(5, 30)),
            'completed_at' => now(),
            'status' => 'submitted',
        ]);

        return $attempt;
    }

    /**
     * Vérifier si un utilisateur a déjà réussi ce quiz.
     */
    public function hasPassed(User $user, Quiz $quiz): bool
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->where('passed', true)
            ->exists();
    }

    /**
     * Obtenir le meilleur score d'un utilisateur pour un quiz.
     */
    public function bestScore(User $user, Quiz $quiz): ?float
    {
        return QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->max('score');
    }
}
