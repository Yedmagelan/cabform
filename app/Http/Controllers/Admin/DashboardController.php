<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Certificate;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Dashboard admin avec statistiques globales.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_learners' => User::role('apprenant')->count(),
            'total_instructors' => User::role('formateur')->count(),
            'total_courses' => Course::count(),
            'published_courses' => Course::published()->count(),
            'total_enrollments' => Enrollment::count(),
            'active_enrollments' => Enrollment::active()->count(),
            'completed_enrollments' => Enrollment::completed()->count(),
            'total_certificates' => Certificate::generated()->count(),
            'total_revenue' => Payment::completed()->sum('amount'),
            'monthly_revenue' => Payment::completed()->whereMonth('paid_at', now()->month)->sum('amount'),
            'pending_orders' => Order::pending()->count(),
        ];

        $recentEnrollments = Enrollment::with(['user', 'course'])->latest()->take(10)->get();
        $recentPayments = Payment::with(['user', 'order'])->completed()->latest('paid_at')->take(10)->get();

        // Revenus par mois (12 derniers mois)
        $monthlyRevenue = Payment::completed()
            ->where('paid_at', '>=', now()->subMonths(12))
            ->selectRaw('MONTH(paid_at) as month, YEAR(paid_at) as year, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentEnrollments', 'recentPayments', 'monthlyRevenue'));
    }
}
