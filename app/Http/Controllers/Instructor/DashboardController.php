<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\ReportGenerationService;

class DashboardController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $user = auth()->user();
        $stats = $this->reportService->getInstructorStats($user, 'all');

        $courses = $user->courses()->withCount('enrollments')->latest()->take(5)->get();
        $recentEnrollments = Enrollment::whereIn('course_id', $user->courses()->pluck('id'))
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        return view('instructor.dashboard', compact('stats', 'courses', 'recentEnrollments'));
    }
}
