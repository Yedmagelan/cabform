<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    protected $reportService;

    public function __construct(ReportGenerationService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'all');

        $stats = $this->reportService->getInstructorStats($user, $period);

        if ($request->wantsJson()) {
            return response()->json($stats);
        }

        $courses = $user->courses;

        return view('instructor.statistics', compact('stats', 'courses', 'period'));
    }

    public function courseStats(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        
        $totalStudents = $course->enrollments()->count();
        $completedStudents = $course->enrollments()->where('status', 'completed')->count();
        $avgProgress = $course->enrollments()->avg('progress_percentage') ?: 0;
        
        $isSqlite = DB::getDriverName() === 'sqlite';
        $dateFormat = $isSqlite ? "strftime('%Y-%m-%d', created_at)" : "DATE_FORMAT(created_at, '%Y-%m-%d')";

        // Progression moyenne dans le temps
        $progressOverTime = $course->enrollments()
            ->select(DB::raw("{$dateFormat} as date"), DB::raw('avg(progress_percentage) as avg_progress'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('avg_progress', 'date')
            ->toArray();

        // Leçon la plus consultée
        $mostViewedLessons = DB::table('progress')
            ->join('lessons', 'progress.lesson_id', '=', 'lessons.id')
            ->join('modules', 'lessons.module_id', '=', 'modules.id')
            ->where('modules.course_id', $course->id)
            ->select('lessons.title', DB::raw('count(*) as views_count'))
            ->groupBy('lessons.id', 'lessons.title')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        return view('instructor.courses.statistics', compact(
            'course', 
            'totalStudents', 
            'completedStudents', 
            'avgProgress', 
            'progressOverTime', 
            'mostViewedLessons'
        ));
    }
}
