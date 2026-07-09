<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function exportCsv(int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $csvContent = $this->reportService->exportCourseStudentsCsv($course);

        $filename = 'rapport_etudiants_' . $course->id . '_' . date('Ymd_His') . '.csv';

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportPdf(int $courseId)
    {
        $course = auth()->user()->courses()->with('enrollments.user')->findOrFail($courseId);
        $stats = $this->reportService->getInstructorStats(auth()->user(), 'all');

        $pdf = Pdf::loadView('instructor.courses.report-pdf', compact('course', 'stats'))->setPaper('a4', 'landscape');
        
        return $pdf->download('rapport_formation_' . $course->id . '.pdf');
    }
}
