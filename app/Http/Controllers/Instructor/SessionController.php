<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SessionCohort;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    public function index(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $sessions = $course->sessionsCohorts()->orderBy('start_date', 'desc')->paginate(10);

        return view('instructor.sessions.index', compact('course', 'sessions'));
    }

    public function create(int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        return view('instructor.sessions.create', compact('course'));
    }

    public function store(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'max_students' => 'nullable|integer|min:1',
            'is_self_paced' => 'nullable|boolean',
            'csv_file' => 'nullable|file|mimes:csv,txt|max:5120',
        ]);

        $session = SessionCohort::create([
            'course_id' => $course->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'max_students' => $validated['max_students'] ?? null,
            'status' => 'upcoming',
        ]);

        // Gérer l'import CSV si présent
        if ($request->hasFile('csv_file')) {
            $this->enrollFromCsv($session, $request->file('csv_file'));
        }

        return redirect()->route('instructor.sessions.show', [$course->id, $session->id])
            ->with('success', 'Session de cohorte créée avec succès.');
    }

    public function show(int $courseId, int $sessionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $session = $course->sessionsCohorts()->findOrFail($sessionId);

        $enrollments = Enrollment::where('session_cohort_id', $session->id)
            ->with('user')
            ->latest()
            ->paginate(20);

        $allStudents = User::role('apprenant')->active()->get();

        return view('instructor.sessions.show', compact('course', 'session', 'enrollments', 'allStudents'));
    }

    public function addStudent(Request $request, int $courseId, int $sessionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $session = $course->sessionsCohorts()->findOrFail($sessionId);

        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Inscrire l'étudiant s'il n'est pas déjà inscrit
        $exists = Enrollment::where('user_id', $request->user_id)
            ->where('course_id', $course->id)
            ->first();

        if ($exists) {
            if ($exists->session_cohort_id == $session->id) {
                return back()->with('error', 'Cet apprenant est déjà inscrit dans cette session.');
            }
            // Déplacer l'apprenant dans la nouvelle session
            $exists->update([
                'session_cohort_id' => $session->id,
            ]);
        } else {
            Enrollment::create([
                'user_id' => $request->user_id,
                'course_id' => $course->id,
                'session_cohort_id' => $session->id,
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

            $session->increment('enrolled_count');
        }

        return back()->with('success', 'Apprenant inscrit avec succès.');
    }

    public function removeStudent(int $courseId, int $sessionId, int $enrollmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $session = $course->sessionsCohorts()->findOrFail($sessionId);

        $enrollment = Enrollment::where('session_cohort_id', $session->id)->findOrFail($enrollmentId);
        $enrollment->delete();

        $session->decrement('enrolled_count');

        return back()->with('success', 'Apprenant retiré de la session.');
    }

    public function close(int $courseId, int $sessionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $session = $course->sessionsCohorts()->findOrFail($sessionId);

        $session->update([
            'status' => 'completed',
        ]);

        // Verrouiller les inscriptions de cette session
        Enrollment::where('session_cohort_id', $session->id)
            ->where('status', 'active')
            ->update(['status' => 'completed', 'completed_at' => now()]);

        return back()->with('success', 'La session a été clôturée. Les inscriptions actives associées sont marquées comme complétées.');
    }

    /**
     * Helper pour inscrire à partir d'un fichier CSV.
     */
    private function enrollFromCsv(SessionCohort $session, $file): void
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        
        // Sauter la ligne d'entête si elle existe
        $header = fgetcsv($handle, 1000, ',');
        
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            $email = trim($row[1]);
            $nameParts = explode(' ', trim($row[0]), 2);
            $firstName = $nameParts[0] ?? 'Apprenant';
            $lastName = $nameParts[1] ?? 'LMS';

            // Trouver ou créer l'utilisateur
            $user = User::firstOrCreate(['email' => $email], [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => bcrypt('password123'), // mot de passe par défaut
                'status' => 'active',
            ]);

            if (!$user->hasRole('apprenant')) {
                $user->assignRole('apprenant');
            }

            // Inscrire
            $exists = Enrollment::where('user_id', $user->id)
                ->where('course_id', $session->course_id)
                ->first();

            if (!$exists) {
                Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $session->course_id,
                    'session_cohort_id' => $session->id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);
                $session->increment('enrolled_count');
            }
        }

        fclose($handle);
    }
}
