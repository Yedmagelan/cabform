<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificateService;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    protected $certService;

    public function __construct(CertificateService $certService)
    {
        $this->certService = $certService;
    }

    public function index(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $query = $course->certificates()->with(['user', 'enrollment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest('issued_at')->paginate(20);

        return view('instructor.courses.certificates', compact('course', 'certificates'));
    }

    public function generate(Request $request, int $courseId, int $studentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $student = User::findOrFail($studentId);

        $enrollment = Enrollment::where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->firstOrFail();

        // Générer le certificat via le service
        $cert = $this->certService->generate($student, $course, $enrollment, $enrollment->progress_percentage);

        return back()->with('success', 'Certificat généré avec succès pour ' . $student->full_name);
    }

    public function revoke(Request $request, int $courseId, int $certificateId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $certificate = $course->certificates()->findOrFail($certificateId);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $this->certService->revoke($certificate, $request->reason);

        return back()->with('success', 'Le certificat a été révoqué avec succès.');
    }
}
