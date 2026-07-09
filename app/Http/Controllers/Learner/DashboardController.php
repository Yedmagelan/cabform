<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\Message;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function pendingActivation()
    {
        $user = auth()->user();
        if ($user->status !== 'pending') {
            return redirect()->route('learner.dashboard');
        }

        // Get the pending enrollment
        $pendingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->with('course')
            ->first();

        $course = $pendingEnrollment ? $pendingEnrollment->course : null;

        return view('learner.pending-activation', compact('user', 'course'));
    }

    public function index()
    {
        $user = auth()->user();

        // 1. GREETING HOUR
        $hour = date('H');
        $greeting = $hour < 18 ? 'Bonjour' : 'Bonsoir';
        $greetingColor = $hour < 18 ? '#eab308' : '#818cf8'; // Yellow for day, indigo for night

        // 2. USER LEVEL BADGE STATUS
        $completedCount = Enrollment::where('user_id', $user->id)->where('status', 'completed')->count();
        $levelStatus = match(true) {
            $completedCount >= 10 => 'Master',
            $completedCount >= 5  => 'Expert',
            $completedCount >= 2  => 'Intermédiaire',
            default => 'Débutant'
        };

        // 3. STATS CARDS KPIs
        $enrollmentsQuery = Enrollment::where('user_id', $user->id)->with(['course.category', 'course.instructor']);
        $allEnrollments = $enrollmentsQuery->get();
        
        $activeEnrollments = $allEnrollments->where('status', 'active');
        $completedEnrollments = $allEnrollments->where('status', 'completed');
        
        $certificatesCount = Certificate::where('user_id', $user->id)->generated()->count();
        
        $avgProgress = $allEnrollments->count() > 0 ? round($allEnrollments->avg('progress_percentage'), 1) : 0;
        
        $timeSpentSeconds = Progress::where('user_id', $user->id)->sum('time_spent_seconds');
        $learningHours = round($timeSpentSeconds / 3600, 1);

        $upcomingDeadlinesCount = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<', now()->addDays(7))
            ->count();

        $avgQuizPercent = $user->quizAttempts()->avg('percentage') ?? 0;
        $satisfactionRate = $avgQuizPercent > 0 ? round(($avgQuizPercent / 100) * 5, 1) : 4.5;

        // 4. RECOMMENDED COURSES
        $enrolledCatIds = $allEnrollments->pluck('course.category_id')->unique()->filter();
        $recommendedCourses = Course::where('status', 'published')
            ->whereNotIn('id', $allEnrollments->pluck('course_id'))
            ->when($enrolledCatIds->isNotEmpty(), function($q) use ($enrolledCatIds) {
                $q->whereIn('category_id', $enrolledCatIds);
            })
            ->limit(3)
            ->get();
        if ($recommendedCourses->isEmpty()) {
            $recommendedCourses = Course::where('status', 'published')
                ->whereNotIn('id', $allEnrollments->pluck('course_id'))
                ->limit(3)
                ->get();
        }

        // 5. TIMELINE DEADLINES
        $courseIds = $allEnrollments->pluck('course_id')->toArray();
        
        $assignments = Assignment::whereIn('course_id', $courseIds)
            ->whereNotNull('due_date')
            ->where('due_date', '>', now())
            ->get()
            ->map(function($a) {
                return [
                    'title' => 'Devoir : ' . $a->title,
                    'due_date' => $a->due_date,
                    'type' => 'assignment',
                    'url' => route('learner.assignment.show', [$a->course->slug, $a->id])
                ];
            });

        $quizzes = Quiz::whereIn('course_id', $courseIds)
            ->get()
            ->filter(function($q) {
                return isset($q->meta_data['due_date']);
            })
            ->map(function($q) {
                return [
                    'title' => 'Quiz : ' . $q->title,
                    'due_date' => \Carbon\Carbon::parse($q->meta_data['due_date']),
                    'type' => 'quiz',
                    'url' => route('learner.quiz.show', [$q->course->slug, $q->id])
                ];
            });

        $timeline = $assignments->concat($quizzes)->sortBy('due_date')->take(5);

        // 6. CERTIFICATES GALLERY
        $certificates = Certificate::where('user_id', $user->id)->generated()->with('course')->latest()->get();

        // 7. NOTIFICATIONS
        $unreadNotifications = $user->unreadNotifications()->take(5)->get();
        $unreadMessages = Message::where('receiver_id', $user->id)->where('is_read', false)->count();

        // Paginate active courses initially
        $activeCoursesPaginated = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.category', 'course.instructor'])
            ->latest()
            ->paginate(6);

        return view('learner.dashboard', compact(
            'greeting', 'greetingColor', 'levelStatus',
            'activeEnrollments', 'completedEnrollments', 'certificatesCount',
            'avgProgress', 'learningHours', 'upcomingDeadlinesCount', 'satisfactionRate',
            'recommendedCourses', 'timeline', 'certificates',
            'unreadNotifications', 'unreadMessages', 'activeCoursesPaginated'
        ));
    }

    /**
     * Ajax courses filter/sorting/pagination.
     */
    public function coursesAjax(Request $request)
    {
        $user = auth()->user();
        $sort = $request->input('sort', 'date');
        $search = $request->input('search');

        $query = Enrollment::where('user_id', $user->id)
            ->where('status', 'active')
            ->with(['course.category', 'course.instructor']);

        if ($search) {
            $query->whereHas('course', function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($sort === 'progression') {
            $query->orderByDesc('progress_percentage');
        } elseif ($sort === 'title') {
            $query->join('courses', 'enrollments.course_id', '=', 'courses.id')
                  ->select('enrollments.*')
                  ->orderBy('courses.title', 'asc');
        } else {
            $query->orderByDesc('enrolled_at');
        }

        $activeCoursesPaginated = $query->paginate(6);

        return view('learner.course._courses_list', compact('activeCoursesPaginated'))->render();
    }

    public function certificates()
    {
        $certificates = Certificate::where('user_id', auth()->id())->with(['course', 'template'])->latest()->get();
        return view('learner.certificates', compact('certificates'));
    }

    public function profile()
    {
        $user = auth()->user()->load('profile');
        return view('learner.profile', compact('user'));
    }

    /**
     * Liste des commandes de l'apprenant.
     */
    public function orders()
    {
        $orders = auth()->user()->orders()->with('items.course')->latest()->paginate(10);
        return view('learner.orders.index', compact('orders'));
    }

    /**
     * Détails d'une commande.
     */
    public function orderShow(int $id)
    {
        $order = auth()->user()->orders()->with('items.course', 'payment')->findOrFail($id);
        return view('learner.orders.show', compact('order'));
    }
}
