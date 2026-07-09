<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentProgressController extends Controller
{
    protected $progressService;

    public function __construct(StudentProgressService $progressService)
    {
        $this->progressService = $progressService;
    }

    public function index(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $query = $course->enrollments()->with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $enrollments = $query->latest()->paginate(20);

        return view('instructor.students.index', compact('course', 'enrollments'));
    }

    public function show(int $courseId, int $studentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $student = User::findOrFail($studentId);

        $details = $this->progressService->getDetailedProgress($student, $course);

        if (empty($details)) {
            return redirect()->route('instructor.students.index', $course->id)
                ->with('error', 'Aucune inscription trouvée pour cet apprenant.');
        }

        return view('instructor.students.show', array_merge(['course' => $course, 'student' => $student], $details));
    }

    public function bulkAction(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:enrollments,id',
            'action' => 'required|in:activate,suspend,remove',
        ]);

        $query = Enrollment::whereIn('id', $request->ids)->where('course_id', $course->id);

        if ($request->action === 'activate') {
            $query->update(['status' => 'active']);
            $msg = 'Inscriptions activées.';
        } elseif ($request->action === 'suspend') {
            $query->update(['status' => 'suspended']);
            $msg = 'Inscriptions suspendues.';
        } elseif ($request->action === 'remove') {
            $query->delete();
            $msg = 'Inscriptions retirées.';
        }

        return response()->json(['success' => true, 'message' => $msg]);
    }

    public function exportPdf(int $courseId, int $studentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $student = User::findOrFail($studentId);
        $details = $this->progressService->getDetailedProgress($student, $course);

        if (empty($details)) {
            return back()->with('error', 'Données introuvables.');
        }

        $pdf = Pdf::loadView('instructor.students.report', array_merge([
            'course' => $course,
            'student' => $student
        ], $details))->setPaper('a4', 'portrait');

        return $pdf->download('rapport_apprenant_' . $student->id . '.pdf');
    }
}
