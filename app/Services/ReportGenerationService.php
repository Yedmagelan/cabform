<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportGenerationService
{
    /**
     * Compiler les statistiques globales d'un formateur.
     */
    public function getInstructorStats(User $instructor, ?string $period = 'all'): array
    {
        $courses = $instructor->courses;
        $courseIds = $courses->pluck('id')->toArray();

        $queryEnrollments = Enrollment::whereIn('course_id', $courseIds);

        // Appliquer la période si spécifiée
        if ($period === 'month') {
            $queryEnrollments->where('created_at', '>=', now()->startOfMonth());
        } elseif ($period === '3_months') {
            $queryEnrollments->where('created_at', '>=', now()->subMonths(3));
        } elseif ($period === '6_months') {
            $queryEnrollments->where('created_at', '>=', now()->subMonths(6));
        } elseif ($period === 'year') {
            $queryEnrollments->where('created_at', '>=', now()->startOfYear());
        }

        $enrollments = $queryEnrollments->get();

        $totalEnrollmentsCount = $enrollments->count();
        $avgProgress = $totalEnrollmentsCount > 0 ? round($enrollments->avg('progress_percentage'), 1) : 0;

        $completedCount = $enrollments->where('status', 'completed')->count();
        $completionRate = $totalEnrollmentsCount > 0 ? round(($completedCount / $totalEnrollmentsCount) * 100, 1) : 0;

        $certificatesCount = Certificate::whereIn('course_id', $courseIds)->count();
        $satisfactionRate = Review::whereIn('course_id', $courseIds)->avg('rating') ?: 5.0;

        $isSqlite = DB::getDriverName() === 'sqlite';
        $monthFormat = $isSqlite ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $monthlyEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->select(DB::raw("{$monthFormat} as month"), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->limit(12)
            ->pluck('count', 'month')
            ->toArray();

        // Remplir les mois vides
        $monthlyChartData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = now()->subMonths($i)->format('Y-m');
            $monthlyChartData[$m] = $monthlyEnrollments[$m] ?? 0;
        }

        // Complétude par cours
        $completionPerCourse = [];
        foreach ($courses as $course) {
            $cEnrollments = $course->enrollments;
            $cCount = $cEnrollments->count();
            $cCompleted = $cEnrollments->where('status', 'completed')->count();
            $rate = $cCount > 0 ? round(($cCompleted / $cCount) * 100, 1) : 0;

            $completionPerCourse[$course->title] = [
                'enrollments' => $cCount,
                'completion_rate' => $rate,
            ];
        }

        return [
            'total_courses' => $courses->count(),
            'total_students' => $totalEnrollmentsCount,
            'avg_progress' => $avgProgress,
            'completion_rate' => $completionRate,
            'total_certificates' => $certificatesCount,
            'satisfaction_rate' => round($satisfactionRate, 1),
            'monthly_enrollments' => $monthlyChartData,
            'completion_per_course' => $completionPerCourse,
        ];
    }

    /**
     * Générer le contenu CSV pour l'export des étudiants d'un cours.
     */
    public function exportCourseStudentsCsv(Course $course): string
    {
        $enrollments = $course->enrollments()->with('user')->get();

        $output = "Nom,Email,Date Inscription,Progression (%),Statut\n";

        foreach ($enrollments as $enrollment) {
            $name = $enrollment->user->full_name;
            $email = $enrollment->user->email;
            $date = $enrollment->created_at->format('Y-m-d H:i');
            $progress = $enrollment->progress_percentage;
            $status = $enrollment->status;

            $output .= "\"{$name}\",\"{$email}\",\"{$date}\",{$progress},\"{$status}\"\n";
        }

        return $output;
    }
}
