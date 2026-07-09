<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Assignment;
use App\Services\EnrollmentService;
use App\Services\ProgressService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(
        protected EnrollmentService $enrollmentService,
        protected ProgressService $progressService,
    ) {}

    /**
     * Afficher le lecteur de cours.
     */
    public function player(string $slug, ?int $lessonId = null)
    {
        $course = Course::where('slug', $slug)
            ->with(['modules.lessons', 'instructor.profile', 'modules.quizzes'])
            ->firstOrFail();

        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment || !in_array($enrollment->status, ['active', 'completed'])) {
            return redirect()->route('checkout', $course->slug)
                ->with('info', 'Votre inscription est en attente de paiement ou de validation.');
        }

        // Première leçon ou leçon spécifique
        if ($lessonId) {
            $currentLesson = Lesson::findOrFail($lessonId);
        } else {
            $currentLesson = $course->modules()->first()?->lessons()->orderBy('sort_order')->first();
        }

        if (!$currentLesson) {
            return redirect()->route('learner.dashboard')->with('error', 'Cette formation ne contient aucune leçon.');
        }

        // Déblocage séquentiel
        if (!$this->progressService->isLessonUnlocked($enrollment, $currentLesson)) {
            return redirect()->route('learner.course.player', $course->slug)
                ->with('error', 'Vous devez compléter la leçon précédente avant d\'accéder à celle-ci.');
        }

        $currentLesson->load('resources');
        $completedLessons = $this->progressService->getCompletedLessonIds($enrollment);

        $progress = \App\Models\Progress::where('enrollment_id', $enrollment->id)
            ->where('lesson_id', $currentLesson->id)
            ->first();
        $videoPosition = $progress?->video_position_seconds ?? 0;

        return view('learner.course.player', compact(
            'course', 'enrollment', 'currentLesson', 'completedLessons', 'videoPosition'
        ));
    }

    /**
     * Mettre à jour la position de la vidéo (Ajax).
     */
    public function updateVideoPosition(Request $request, string $slug, int $lessonId)
    {
        $request->validate([
            'position' => 'required|numeric|min:0',
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();
        $lesson = Lesson::findOrFail($lessonId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $progress = \App\Models\Progress::firstOrCreate([
            'enrollment_id' => $enrollment->id,
            'lesson_id' => $lesson->id,
        ], [
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->course_id,
            'status' => 'in_progress',
        ]);

        $progress->update([
            'video_position_seconds' => $request->position,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Marquer une leçon comme complétée (Ajax).
     */
    public function completeLesson(Request $request, string $slug, int $lessonId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $lesson = Lesson::findOrFail($lessonId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->where('status', 'active')
            ->firstOrFail();

        $this->progressService->markLessonComplete($enrollment, $lesson);

        return response()->json([
            'success' => true,
            'progress' => $enrollment->fresh()->progress_percentage,
        ]);
    }

    /**
     * Tracker le temps passé sur une leçon (Ajax).
     */
    public function trackTime(Request $request, string $slug, int $lessonId)
    {
        $request->validate(['seconds' => 'required|integer|min:1']);

        $course = Course::where('slug', $slug)->firstOrFail();
        $lesson = Lesson::findOrFail($lessonId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $this->progressService->trackTime($enrollment, $lesson, $request->seconds);

        return response()->json(['success' => true]);
    }

    /**
     * S'inscrire à un cours gratuit.
     */
    public function enrollFree(Request $request, string $slug)
    {
        $course = Course::where('slug', $slug)->where('is_free', true)->firstOrFail();

        $enrollment = $this->enrollmentService->enroll(auth()->user(), $course);

        return redirect()->route('learner.course.player', $course->slug)
            ->with('success', 'Vous êtes maintenant inscrit à cette formation !');
    }

    /**
     * Afficher le devoir.
     */
    public function assignmentShow(string $slug, int $assignmentId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $assignment = Assignment::findOrFail($assignmentId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $submission = \App\Models\Submission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('learner.course.assignment', compact('course', 'assignment', 'enrollment', 'submission'));
    }

    /**
     * Soumettre un devoir.
     */
    public function assignmentSubmit(Request $request, string $slug, int $assignmentId)
    {
        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:20480', // Max 20MB
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();
        $assignment = Assignment::findOrFail($assignmentId);
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $filePath = null;
        $fileName = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('submissions', 'public');
            $fileName = $request->file('file')->getClientOriginalName();
        }

        \App\Models\Submission::updateOrCreate([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
        ], [
            'content' => $request->content,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()->route('learner.course.player', [
            'slug' => $course->slug
        ])->with('success', 'Devoir soumis avec succès !');
    }

    /**
     * Enregistrer un signet/note.
     */
    public function storeBookmark(Request $request, string $slug, int $lessonId)
    {
        $request->validate([
            'note' => 'required|string',
        ]);

        $course = Course::where('slug', $slug)->firstOrFail();

        $bookmark = \App\Models\Bookmark::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'lesson_id' => $lessonId,
            'note' => $request->note,
        ]);

        return response()->json([
            'success' => true,
            'bookmark' => $bookmark
        ]);
    }

    /**
     * Récupérer les signets/notes d'une leçon.
     */
    public function getBookmarks(string $slug, int $lessonId)
    {
        $course = Course::where('slug', $slug)->firstOrFail();
        $bookmarks = \App\Models\Bookmark::where('user_id', auth()->id())
            ->where('lesson_id', $lessonId)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'bookmarks' => $bookmarks
        ]);
    }

    /**
     * Supprimer un signet.
     */
    public function deleteBookmark(string $slug, int $lessonId, int $bookmarkId)
    {
        $bookmark = \App\Models\Bookmark::where('user_id', auth()->id())
            ->where('lesson_id', $lessonId)
            ->where('id', $bookmarkId)
            ->firstOrFail();

        $bookmark->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
