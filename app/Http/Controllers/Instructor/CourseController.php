<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Coupon;
use App\Services\CoursePublishingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    protected $publishService;

    public function __construct(CoursePublishingService $publishService)
    {
        $this->publishService = $publishService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->courses()->withCount(['modules', 'enrollments']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(10);
        return view('instructor.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::roots()->active()->ordered()->get();
        return view('instructor.courses.create', compact('categories'));
    }

    public function getSubcategories($categoryId)
    {
        $subcategories = Category::where('parent_id', $categoryId)->active()->ordered()->get();
        return response()->json($subcategories);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'level' => 'required|in:debutant,intermediaire,avance,expert',
            'language' => 'required|string|max:5',
        ]);

        $validated['instructor_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['status'] = 'draft';
        $validated['price'] = 0;
        $validated['is_free'] = true;

        if ($request->has('subcategory_id') && !empty($request->subcategory_id)) {
            $validated['category_id'] = $request->subcategory_id; // Store final subcategory
        }

        $course = Course::create($validated);

        return response()->json([
            'success' => true,
            'course_id' => $course->id,
            'message' => 'Brouillon de formation créé avec succès.',
            'redirect' => route('instructor.courses.edit', $course->id)
        ]);
    }

    public function edit(int $id)
    {
        $user = auth()->user();
        $course = $user->courses()->with(['modules.lessons.resources', 'modules.quizzes', 'assignments'])->findOrFail($id);
        $categories = Category::roots()->active()->ordered()->get();
        
        $currentCategory = $course->category;
        $selectedRootId = null;
        $selectedSubId = null;

        if ($currentCategory) {
            if ($currentCategory->parent_id) {
                $selectedRootId = $currentCategory->parent_id;
                $selectedSubId = $currentCategory->id;
            } else {
                $selectedRootId = $currentCategory->id;
            }
        }

        $subcategories = $selectedRootId ? Category::where('parent_id', $selectedRootId)->active()->ordered()->get() : collect();
        $coupons = Coupon::where('is_active', true)->get();
        $allCourses = $user->courses()->where('id', '!=', $course->id)->get();

        return view('instructor.courses.edit', compact(
            'course', 
            'categories', 
            'subcategories', 
            'selectedRootId', 
            'selectedSubId',
            'coupons',
            'allCourses'
        ));
    }

    public function update(Request $request, int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'description' => 'required|string',
            'content' => 'nullable|string',
            'level' => 'required|in:debutant,intermediaire,avance,expert',
            'language' => 'required|string|max:5',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'is_free' => 'nullable|boolean',
            'duration_hours' => 'nullable|integer|min:0',
            'objectives' => 'nullable|string',
            'prerequisites' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'is_certified' => 'nullable|boolean',
            'sequential_unlock' => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->has('subcategory_id') && !empty($request->subcategory_id)) {
            $validated['category_id'] = $request->subcategory_id;
        }

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
        }

        // Normaliser les booleans
        $validated['is_free'] = $request->has('is_free');
        if ($validated['is_free']) {
            $validated['price'] = 0;
            $validated['sale_price'] = null;
        } else {
            $validated['price'] = $request->input('price', 0);
            $validated['sale_price'] = $request->input('sale_price');
        }
        $validated['is_certified'] = $request->has('is_certified');
        $validated['sequential_unlock'] = $request->has('sequential_unlock');

        // Gérer les options avancées dans meta_data
        $meta = $course->meta_data ?? [];
        $meta['certificate_min_progress'] = $request->input('certificate_min_progress', 100);
        $meta['coupon_ids'] = $request->input('coupon_ids', []);
        $validated['meta_data'] = $meta;

        $course->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Sauvegarde automatique réussie.']);
        }

        return back()->with('success', 'La formation a été mise à jour.');
    }

    public function autosave(Request $request, int $id)
    {
        return $this->update($request, $id);
    }

    public function publish(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);

        if (auth()->user()->isAdmin()) {
            $this->publishService->publish($course);
            return back()->with('success', 'La formation est maintenant en ligne !');
        }

        $submitted = $this->publishService->submitForReview($course);
        if (!$submitted) {
            $validation = $this->publishService->validateCompleteness($course);
            return back()->withErrors($validation['errors'])->with('error', 'La formation est incomplète.');
        }

        return back()->with('success', 'Formation soumise pour révision.');
    }

    public function duplicate(int $id)
    {
        $course = Course::with(['modules.lessons.resources', 'modules.quizzes'])->findOrFail($id);

        if (!auth()->user()->isAdmin() && $course->instructor_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $newCourse = $course->replicate();
        $newCourse->title = 'Copie de ' . $course->title;
        $newCourse->slug = Str::slug($newCourse->title) . '-' . Str::random(5);
        $newCourse->status = 'draft';
        $newCourse->published_at = null;
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

            foreach ($module->quizzes as $quiz) {
                $newQuiz = $quiz->replicate();
                $newQuiz->course_id = $newCourse->id;
                $newQuiz->module_id = $newModule->id;
                $newQuiz->save();
            }
        }

        return redirect()->route('instructor.courses')->with('success', 'Formation dupliquée comme brouillon.');
    }

    public function archive(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);
        $this->publishService->archive($course);
        return back()->with('success', 'Formation archivée.');
    }

    public function restore(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);
        $this->publishService->restore($course);
        return back()->with('success', 'Formation restaurée en brouillon.');
    }

    public function destroy(int $id)
    {
        $course = auth()->user()->courses()->findOrFail($id);
        $course->delete();
        return redirect()->route('instructor.courses')->with('success', 'Formation supprimée.');
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
}
