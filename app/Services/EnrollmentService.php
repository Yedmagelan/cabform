<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Progress;
use App\Models\AuditLog;

class EnrollmentService
{
    /**
     * Inscrire un utilisateur à une formation.
     */
    public function enroll(User $user, Course $course, ?int $orderId = null, string $status = 'active'): Enrollment
    {
        // Vérifier si déjà inscrit (actif ou complété)
        $existingActive = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if ($existingActive) {
            return $existingActive;
        }

        // Vérifier s'il y a déjà une inscription en attente (pending)
        $existingPending = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->first();

        if ($existingPending) {
            if ($status === 'active') {
                $existingPending->update([
                    'status' => 'active',
                    'order_id' => $orderId,
                    'enrolled_at' => now(),
                ]);
                $course->increment('enrollment_count');
            }
            return $existingPending;
        }

        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'order_id' => $orderId,
            'status' => $status,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        if ($status === 'active') {
            // Incrémenter le compteur d'inscriptions uniquement quand c'est actif
            $course->increment('enrollment_count');
        }

        AuditLog::log('enrollment', $user, 'App\Models\Enrollment', $enrollment->id,
            "Inscription (" . ($status === 'active' ? 'Active' : 'En attente') . ") à la formation: {$course->title}");

        return $enrollment;
    }

    /**
     * Compléter une inscription.
     */
    public function complete(Enrollment $enrollment): void
    {
        $enrollment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);

        AuditLog::log('enrollment_complete', $enrollment->user, 'App\Models\Enrollment', $enrollment->id,
            "Formation terminée: {$enrollment->course->title}");
    }

    /**
     * Annuler une inscription.
     */
    public function cancel(Enrollment $enrollment): void
    {
        $enrollment->update(['status' => 'cancelled']);
        $enrollment->course->decrement('enrollment_count');
    }
}
