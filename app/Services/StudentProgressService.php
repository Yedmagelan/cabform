<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Progress;
use App\Models\QuizAttempt;
use App\Models\Submission;
use App\Models\Certificate;

class StudentProgressService
{
    /**
     * Obtenir des statistiques détaillées d'un apprenant pour un cours.
     */
    public function getDetailedProgress(User $student, Course $course): array
    {
        $enrollment = Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return [];
        }

        // Calcul de progression des leçons
        $totalLessonsCount = $course->lessons()->count();
        $completedLessonsCount = Progress::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        // Quiz
        $quizAttempts = QuizAttempt::where('user_id', $student->id)
            ->whereIn('quiz_id', $course->quizzes()->pluck('id'))
            ->orderBy('created_at', 'desc')
            ->get();

        $quizAverages = $quizAttempts->groupBy('quiz_id')->map(function ($attempts) {
            return [
                'attempts_count' => $attempts->count(),
                'max_score' => $attempts->max('score'),
                'latest_score' => $attempts->first()->score,
                'passed' => $attempts->contains('passed', true),
            ];
        });

        // Devoirs
        $submissions = Submission::where('user_id', $student->id)
            ->whereIn('assignment_id', $course->assignments()->pluck('id'))
            ->with('assignment')
            ->get();

        // Certificat
        $certificate = Certificate::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->first();

        return [
            'enrollment' => $enrollment,
            'lessons_total' => $totalLessonsCount,
            'lessons_completed' => $completedLessonsCount,
            'progress_percentage' => $enrollment->progress_percentage,
            'time_spent_minutes' => $enrollment->time_spent_minutes,
            'quiz_attempts' => $quizAttempts,
            'quiz_averages' => $quizAverages,
            'submissions' => $submissions,
            'certificate' => $certificate,
        ];
    }

    /**
     * Vérifier les critères d'obtention de certificat et le générer si remplis.
     */
    public function checkAndGenerateCertificate(Enrollment $enrollment): bool
    {
        $course = $enrollment->course;
        $student = $enrollment->user;

        // Si le cours n'est pas certifiant, s'arrêter
        if (!$course->is_certified) {
            return false;
        }

        // Déblocage séquentiel ou non, la progression doit être à 100% (ou selon config cours, ex: 80% progression minimum)
        $progressionRequired = $course->meta_data['certificate_min_progress'] ?? 100;
        if ($enrollment->progress_percentage < $progressionRequired) {
            return false;
        }

        // Vérifier les scores min des quiz si configuré
        $quizzes = $course->quizzes()->where('is_mandatory', true)->get();
        foreach ($quizzes as $quiz) {
            $passed = QuizAttempt::where('user_id', $student->id)
                ->where('quiz_id', $quiz->id)
                ->where('passed', true)
                ->exists();

            if (!$passed) {
                return false;
            }
        }

        // Vérifier que tous les devoirs requis ont été soumis et validés
        $assignments = $course->assignments()->where('is_active', true)->get();
        foreach ($assignments as $assignment) {
            $submission = Submission::where('user_id', $student->id)
                ->where('assignment_id', $assignment->id)
                ->where('status', 'graded')
                ->where('passed', true)
                ->first();

            if (!$submission) {
                return false;
            }
        }

        // Si toutes les conditions sont remplies, générer le certificat
        app(CertificateService::class)->generate($student, $course, $enrollment);
        return true;
    }
}
