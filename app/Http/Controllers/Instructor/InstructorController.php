<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    // ── Dashboard ────────────────────────────────────────────
    public function dashboard()
    {
        $user = auth()->user();
        $courses = $user->courses()->withCount('enrollments')->latest()->get();
        $totalStudents = Enrollment::whereIn('course_id', $courses->pluck('id'))->distinct('user_id')->count('user_id');
        $totalRevenue = $courses->sum(fn($c) => $c->enrollments_count * $c->price);

        return view('instructor.dashboard', compact('courses', 'totalStudents', 'totalRevenue'));
    }

    // ── CRUD Cours ───────────────────────────────────────────
    public function courses()
    {
        $courses = auth()->user()->courses()->withCount(['modules', 'enrollments'])->latest()->paginate(10);
        return view('instructor.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('instructor.courses.create', compact('categories'));
    }

    public function storeCourse(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'level' => 'required|in:debutant,intermediaire,avance,expert',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'is_certified' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['instructor_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'draft';
        $validated['is_free'] = ($validated['price'] == 0);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course = Course::create($validated);

        return redirect()->route('instructor.courses.edit', $course->id)
            ->with('success', 'Formation créée. Ajoutez maintenant les modules et leçons.');
    }

    public function editCourse(int $id)
    {
        $course = auth()->user()->courses()->with(['modules.lessons', 'modules.quiz'])->findOrFail($id);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('instructor.courses.edit', compact('course', 'categories'));
    }

    public function updateCourse(Request $request, int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'level' => 'required|in:debutant,intermediaire,avance,expert',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'is_certified' => 'boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $validated['is_free'] = ($validated['price'] == 0);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        $course->update($validated);

        return back()->with('success', 'Formation mise à jour.');
    }

    public function publishCourse(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $course->update(['status' => 'published', 'published_at' => now()]);
            return back()->with('success', 'Formation publiée !');
        }

        $course->update(['status' => 'pending_review']);
        return back()->with('success', 'Formation soumise pour révision !');
    }

    public function duplicateCourse(int $id)
    {
        $course = Course::with('modules.lessons.resources')->findOrFail($id);

        if (!auth()->user()->isAdmin() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $newCourse = $course->replicate();
        $newCourse->title = 'Copie de ' . $course->title;
        $newCourse->slug = $course->slug . '-copie-' . time();
        $newCourse->status = 'draft';
        $newCourse->published_at = null;
        $newCourse->version = 1;
        $newCourse->save();

        foreach ($course->modules as $module) {
            $newModule = $module->replicate();
            $newModule->course_id = $newCourse->id;
            $newModule->save();

            foreach ($module->lessons as $lesson) {
                $newLesson = $lesson->replicate();
                $newLesson->module_id = $newModule->id;
                $newLesson->save();

                foreach ($lesson->resources as $resource) {
                    $newResource = $resource->replicate();
                    $newResource->lesson_id = $newLesson->id;
                    $newResource->save();
                }
            }
        }

        return back()->with('success', 'Formation dupliquée avec succès comme brouillon.');
    }

    public function incrementVersion(int $id)
    {
        $course = Course::findOrFail($id);

        if (!auth()->user()->isAdmin() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $course->increment('version');

        return back()->with('success', 'Nouvelle version (' . $course->version . ') créée avec succès.');
    }

    public function deleteCourse(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);
        $course->delete();
        return redirect()->route('instructor.courses')->with('success', 'Formation supprimée.');
    }

    // ── Modules ──────────────────────────────────────────────
    public function storeModule(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['course_id'] = $course->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['sort_order'] = $course->modules()->count();
        $validated['is_active'] = true;

        Module::create($validated);

        return back()->with('success', 'Module ajouté.');
    }

    public function updateModule(Request $request, int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);

        $module->update($request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]));

        return back()->with('success', 'Module mis à jour.');
    }

    public function deleteModule(int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $course->modules()->findOrFail($moduleId)->delete();
        return back()->with('success', 'Module supprimé.');
    }

    // ── Leçons ───────────────────────────────────────────────
    public function storeLesson(Request $request, int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,text,pdf,audio',
            'content' => 'nullable|string',
            'content_file' => 'nullable|file|max:51200',
            'duration_minutes' => 'required|integer|min:1',
            'is_free_preview' => 'boolean',
        ]);

        $validated['module_id'] = $module->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['sort_order'] = $module->lessons()->count();
        $validated['is_active'] = true;

        if ($request->hasFile('content_file')) {
            $validated['content'] = $request->file('content_file')->store('lessons', 'public');
        }

        Lesson::create($validated);

        return back()->with('success', 'Leçon ajoutée.');
    }

    public function deleteLesson(int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $module->lessons()->findOrFail($lessonId)->delete();
        return back()->with('success', 'Leçon supprimée.');
    }

    // ── Quiz ─────────────────────────────────────────────────
    public function storeQuiz(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'module_id' => 'nullable|exists:modules,id',
            'type' => 'required|in:quiz,final_exam',
            'passing_score' => 'required|integer|min:1|max:100',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1',
        ]);

        $validated['course_id'] = $course->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['is_active'] = true;

        $quiz = Quiz::create($validated);

        return redirect()->route('instructor.quiz.edit', [$courseId, $quiz->id])
            ->with('success', 'Quiz créé. Ajoutez les questions.');
    }

    public function editQuiz(int $courseId, int $quizId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $quiz = Quiz::with('questions.answers')->findOrFail($quizId);
        return view('instructor.quiz.edit', compact('course', 'quiz'));
    }

    public function storeQuestion(Request $request, int $courseId, int $quizId)
    {
        auth()->user()->courses()->findOrFail($courseId);
        $quiz = Quiz::findOrFail($quizId);

        $validated = $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        $question = Question::create([
            'quiz_id' => $quiz->id,
            'question_text' => $validated['question_text'],
            'type' => $validated['type'],
            'points' => $validated['points'],
            'sort_order' => $quiz->questions()->count(),
        ]);

        foreach ($validated['answers'] as $answerData) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answerData['text'],
                'is_correct' => $answerData['is_correct'] ?? false,
            ]);
        }

        return back()->with('success', 'Question ajoutée.');
    }

    // ── Suivi étudiants ──────────────────────────────────────
    public function students(int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $enrollments = $course->enrollments()->with('user')->latest()->paginate(20);

        return view('instructor.students', compact('course', 'enrollments'));
    }
}
