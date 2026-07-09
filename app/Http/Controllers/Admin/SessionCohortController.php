<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SessionCohort;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SessionCohortController extends Controller
{
    /**
     * Display a listing of sessions.
     */
    public function index(Request $request)
    {
        Gate::authorize('enrollments.view'); // Reuse enrollment permission or similar

        $query = SessionCohort::with('course')->withCount('enrollments');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $sessions = $query->latest()->paginate(15);

        // Calculate KPIs
        $totalSessions = SessionCohort::count();
        $activeSessions = SessionCohort::where('status', 'active')->count();
        $completedSessions = SessionCohort::where('status', 'completed')->count();
        
        $totalCapacity = SessionCohort::sum('max_students');
        $totalEnrolled = SessionCohort::sum('enrolled_count');
        $occupancyRate = $totalCapacity > 0 ? round(($totalEnrolled / $totalCapacity) * 100, 1) : 0;

        return view('admin.sessions.index', compact('sessions', 'totalSessions', 'activeSessions', 'completedSessions', 'occupancyRate'));
    }

    /**
     * Show the form for creating a new session.
     */
    public function create()
    {
        Gate::authorize('enrollments.view');
        $courses = Course::published()->get();
        return view('admin.sessions.create', compact('courses'));
    }

    /**
     * Store a newly created session.
     */
    public function store(Request $request)
    {
        Gate::authorize('enrollments.view');

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'enrollment_deadline' => 'nullable|date|before_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        $session = SessionCohort::create($validated);

        AuditLog::log('session_create', auth()->user(), 'App\Models\SessionCohort', $session->id, "Session de cohorte créée : {$session->name}");

        return redirect()->route('admin.sessions.index')->with('success', 'Session créée avec succès.');
    }

    /**
     * Display the specified session details.
     */
    public function show(int $id)
    {
        Gate::authorize('enrollments.view');

        $session = SessionCohort::with(['course', 'enrollments.user'])->findOrFail($id);

        $enrollments = $session->enrollments()->with('user')->paginate(15);
        $avgProgress = $session->enrollments()->avg('progress_percentage') ?? 0;
        
        return view('admin.sessions.show', compact('session', 'enrollments', 'avgProgress'));
    }

    /**
     * Show the form for editing the session.
     */
    public function edit(int $id)
    {
        Gate::authorize('enrollments.view');
        $session = SessionCohort::findOrFail($id);
        $courses = Course::published()->get();
        return view('admin.sessions.edit', compact('session', 'courses'));
    }

    /**
     * Update the session in storage.
     */
    public function update(Request $request, int $id)
    {
        Gate::authorize('enrollments.view');

        $session = SessionCohort::findOrFail($id);

        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'enrollment_deadline' => 'nullable|date|before_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        $session->update($validated);

        AuditLog::log('session_update', auth()->user(), 'App\Models\SessionCohort', $session->id, "Session de cohorte mise à jour : {$session->name}");

        return redirect()->route('admin.sessions.index')->with('success', 'Session mise à jour avec succès.');
    }

    /**
     * Remove the session from storage.
     */
    public function destroy(int $id)
    {
        Gate::authorize('enrollments.view');

        $session = SessionCohort::findOrFail($id);
        $name = $session->name;
        $session->delete();

        AuditLog::log('session_delete', auth()->user(), 'App\Models\SessionCohort', $id, "Session de cohorte supprimée : {$name}");

        return redirect()->route('admin.sessions.index')->with('success', 'Session supprimée avec succès.');
    }

    /**
     * Close/Complete the session.
     */
    public function close(int $id)
    {
        Gate::authorize('enrollments.view');

        $session = SessionCohort::findOrFail($id);
        $session->update(['status' => 'completed']);

        // Lock submissions or trigger certificates for learners if necessary

        AuditLog::log('session_close', auth()->user(), 'App\Models\SessionCohort', $session->id, "Session de cohorte clôturée : {$session->name}");

        return back()->with('success', 'La session a été clôturée avec succès.');
    }

    /**
     * Duplicate the session.
     */
    public function duplicate(Request $request, int $id)
    {
        Gate::authorize('enrollments.view');

        $session = SessionCohort::findOrFail($id);
        
        $newSession = $session->replicate();
        $newSession->name = $session->name . ' - Copie';
        $newSession->enrolled_count = 0;
        $newSession->status = 'upcoming';
        $newSession->save();

        AuditLog::log('session_duplicate', auth()->user(), 'App\Models\SessionCohort', $session->id, "Session de cohorte dupliquée : {$session->name} (Nouvelle : {$newSession->name})");

        return redirect()->route('admin.sessions.index')->with('success', 'Session dupliquée avec succès comme brouillon/future.');
    }
}
