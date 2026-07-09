<?php

namespace App\Services;

use App\Models\Progress;
use App\Models\Enrollment;
use App\Models\Lesson;

class ProgressService
{
    /**
     * Marquer une leçon comme complétée et recalculer la progression.
     */
    public function markLessonComplete(Enrollment $enrollment, Lesson $lesson): Progress
    {
        $progress = Progress::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
        ], [
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'status' => 'completed',
            'completed_at' => now(),
            'time_spent_seconds' => 0,
        ]);

        if ($progress->status !== 'completed') {
            $progress->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $this->recalculateProgress($enrollment);

        return $progress;
    }

    /**
     * Ajouter du temps passé sur une leçon.
     */
    public function trackTime(Enrollment $enrollment, Lesson $lesson, int $seconds): void
    {
        $progress = Progress::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
        ], [
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'status' => 'in_progress',
        ]);

        $progress->increment('time_spent_seconds', $seconds);
        $enrollment->increment('time_spent_minutes', intdiv($seconds, 60));
    }

    /**
     * Recalculer le pourcentage de progression.
     */
    public function recalculateProgress(Enrollment $enrollment): float
    {
        $course = $enrollment->course()->with('modules.lessons')->first();
        $totalLessons = 0;

        foreach ($course->modules as $module) {
            $totalLessons += $module->lessons->count();
        }

        if ($totalLessons === 0) {
            return 0;
        }

        $completedLessons = Progress::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->count();

        $percentage = round(($completedLessons / $totalLessons) * 100, 2);

        $enrollment->update(['progress_percentage' => $percentage]);

        // Si 100%, marquer comme terminée
        if ($percentage >= 100) {
            app(EnrollmentService::class)->complete($enrollment);
        }

        return $percentage;
    }

    public function getCompletedLessonIds(Enrollment $enrollment): array
    {
        return Progress::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')
            ->pluck('lesson_id')
            ->toArray();
    }

    /**
     * Vérifier si une leçon est débloquée (déblocage séquentiel).
     */
    public function isLessonUnlocked(Enrollment $enrollment, Lesson $lesson): bool
    {
        $course = $enrollment->course;

        if (!$course->sequential_unlock) {
            return true;
        }

        $orderedLessons = $course->lessons;
        $completedLessonIds = $this->getCompletedLessonIds($enrollment);

        foreach ($orderedLessons as $index => $orderedLesson) {
            if ($orderedLesson->id === $lesson->id) {
                if ($index === 0) {
                    return true;
                }

                $previousLesson = $orderedLessons[$index - 1];
                return in_array($previousLesson->id, $completedLessonIds);
            }
        }

        return false;
    }
}
