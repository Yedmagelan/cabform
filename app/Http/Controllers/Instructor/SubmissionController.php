<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Enrollment;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class SubmissionController extends Controller
{
    public function index(Request $request, int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);

        $query = $assignment->submissions()->with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $submissions = $query->latest('submitted_at')->paginate(20);
        $pendingCount = $assignment->submissions()->where('status', 'submitted')->count();

        return view('instructor.assignments.submissions', compact('course', 'assignment', 'submissions', 'pendingCount'));
    }

    public function show(int $courseId, int $assignmentId, int $submissionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        $submission = $assignment->submissions()->with('user')->findOrFail($submissionId);

        // Calculer le retard
        $isLate = false;
        $lateDuration = '';
        if ($assignment->due_date && $submission->submitted_at && $submission->submitted_at->gt($assignment->due_date)) {
            $isLate = true;
            $diff = $submission->submitted_at->diff($assignment->due_date);
            $lateDuration = $diff->format('%d jours, %h heures');
        }

        // Récupérer les tentatives passées
        $previousSubmissions = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $submission->user_id)
            ->where('id', '!=', $submission->id)
            ->oldest()
            ->get();

        return view('instructor.assignments.grade', compact(
            'course', 
            'assignment', 
            'submission', 
            'isLate', 
            'lateDuration',
            'previousSubmissions'
        ));
    }

    public function grade(Request $request, int $courseId, int $assignmentId, int $submissionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        $submission = $assignment->submissions()->findOrFail($submissionId);

        $validated = $request->validate([
            'feedback' => 'nullable|string',
            'rubric_grades' => 'nullable|array',
            'status' => 'required|in:graded,returned',
            'score' => 'nullable|numeric|min:0',
        ]);

        $score = 0;
        $rubricGrades = $validated['rubric_grades'] ?? [];

        if (!empty($assignment->rubric) && !empty($rubricGrades)) {
            // Calculer le score total à partir des critères
            foreach ($assignment->rubric as $criterion) {
                $cId = $criterion['id'] ?? $criterion['title'];
                $cScore = isset($rubricGrades[$cId]['score']) ? floatval($rubricGrades[$cId]['score']) : 0;
                $score += $cScore;
            }
        } else {
            $score = $validated['score'] ?? 0;
        }

        // Borner le score au maximum possible
        if ($score > $assignment->max_score) {
            $score = $assignment->max_score;
        }

        $passed = $score >= $assignment->passing_score;

        $submission->update([
            'score' => $score,
            'passed' => $passed,
            'feedback' => $validated['feedback'] ?? null,
            'rubric_grades' => $rubricGrades,
            'status' => $validated['status'],
            'graded_by' => auth()->id(),
            'graded_at' => now(),
        ]);

        // Mettre à jour la progression de l'apprenant
        $enrollment = Enrollment::where('user_id', $submission->user_id)
            ->where('course_id', $course->id)
            ->first();

        if ($enrollment) {
            // Vérifier et générer le certificat si les conditions sont remplies
            app(StudentProgressService::class)->checkAndGenerateCertificate($enrollment);
        }

        return redirect()->route('instructor.submissions.index', [$course->id, $assignment->id])
            ->with('success', 'Soumission notée avec succès.');
    }

    public function reject(Request $request, int $courseId, int $assignmentId, int $submissionId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        $submission = $assignment->submissions()->findOrFail($submissionId);

        $request->validate([
            'reason' => 'required|string',
        ]);

        $submission->update([
            'status' => 'returned',
            'feedback' => $request->reason,
            'graded_by' => auth()->id(),
            'graded_at' => now(),
            'score' => null,
            'passed' => false,
        ]);

        return redirect()->route('instructor.submissions.index', [$course->id, $assignment->id])
            ->with('success', 'Soumission renvoyée pour correction.');
    }

    public function bulkGrade(Request $request, int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:submissions,id',
            'score' => 'required|numeric|min:0|max:' . $assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $score = floatval($request->score);
        $passed = $score >= $assignment->passing_score;

        Submission::whereIn('id', $request->ids)
            ->where('assignment_id', $assignment->id)
            ->update([
                'score' => $score,
                'passed' => $passed,
                'feedback' => $request->feedback ?? 'Note de masse appliquée.',
                'status' => 'graded',
                'graded_by' => auth()->id(),
                'graded_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => 'Les notes groupées ont été enregistrées.']);
    }

    public function export(int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        $submissions = $assignment->submissions()->whereNotNull('file_path')->with('user')->get();

        if ($submissions->isEmpty()) {
            return back()->with('error', 'Aucun fichier soumis à exporter.');
        }

        $zip = new ZipArchive();
        $zipFileName = 'submissions_' . Str::slug($assignment->title) . '_' . date('Ymd_His') . '.zip';
        $zipFilePath = storage_path('app/public/temp/' . $zipFileName);

        // Créer le dossier temporaire s'il n'existe pas
        if (!file_exists(dirname($zipFilePath))) {
            mkdir(dirname($zipFilePath), 0755, true);
        }

        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($submissions as $submission) {
                if (Storage::disk('public')->exists($submission->file_path)) {
                    $fileContent = Storage::disk('public')->get($submission->file_path);
                    $ext = pathinfo($submission->file_name, PATHINFO_EXTENSION);
                    $fileNameInZip = Str::slug($submission->user->full_name) . '_' . $submission->id . '.' . $ext;
                    $zip->addFromString($fileNameInZip, $fileContent);
                }
            }
            $zip->close();

            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Impossible de créer le fichier ZIP.');
    }
}
