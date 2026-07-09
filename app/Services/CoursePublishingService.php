<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Facades\Log;

class CoursePublishingService
{
    /**
     * Valide si le cours a toutes les informations requises pour être publié/soumis.
     */
    public function validateCompleteness(Course $course): array
    {
        $errors = [];

        if (empty($course->title)) {
            $errors[] = "Le titre de la formation est requis.";
        }
        if (empty($course->description)) {
            $errors[] = "La description courte est requise.";
        }
        if (empty($course->category_id)) {
            $errors[] = "La catégorie est requise.";
        }
        if (empty($course->level)) {
            $errors[] = "Le niveau d'expertise est requis.";
        }
        if (empty($course->objectives)) {
            $errors[] = "Au moins un objectif d'apprentissage est requis.";
        }
        if ($course->modules()->count() === 0) {
            $errors[] = "La formation doit contenir au moins un module.";
        } else {
            $lessonsCount = $course->lessons()->count();
            if ($lessonsCount === 0) {
                $errors[] = "La formation doit contenir au moins une leçon.";
            }
        }

        return [
            'is_complete' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Soumettre pour révision.
     */
    public function submitForReview(Course $course): bool
    {
        $validation = $this->validateCompleteness($course);
        if (!$validation['is_complete']) {
            return false;
        }

        $course->update([
            'status' => 'pending_review',
        ]);

        Log::info("La formation '{$course->title}' (ID: {$course->id}) a été soumise pour approbation.");

        return true;
    }

    /**
     * Publier la formation.
     */
    public function publish(Course $course): void
    {
        $course->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        Log::info("La formation '{$course->title}' (ID: {$course->id}) a été publiée.");
    }

    /**
     * Rejeter la formation avec motif.
     */
    public function reject(Course $course, string $reason): void
    {
        $meta = $course->meta_data ?? [];
        $meta['rejection_reason'] = $reason;
        $meta['rejected_at'] = now()->toDateTimeString();

        $course->update([
            'status' => 'draft',
            'meta_data' => $meta,
        ]);

        Log::info("La formation '{$course->title}' (ID: {$course->id}) a été rejetée. Motif : {$reason}");
    }

    /**
     * Archiver la formation.
     */
    public function archive(Course $course): void
    {
        $course->update([
            'status' => 'archived',
        ]);

        Log::info("La formation '{$course->title}' (ID: {$course->id}) a été archivée.");
    }

    /**
     * Restaurer la formation.
     */
    public function restore(Course $course): void
    {
        $course->update([
            'status' => 'draft',
        ]);

        Log::info("La formation '{$course->title}' (ID: {$course->id}) a été restaurée en brouillon.");
    }
}
