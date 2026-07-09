<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class LessonController extends Controller
{
    public function store(Request $request, int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,text,pdf,audio,slide,embed,scorm',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $validated['module_id'] = $module->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['sort_order'] = $module->lessons()->count();
        $validated['is_active'] = true;

        $lesson = Lesson::create($validated);

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Leçon créée avec succès. Éditez-la ci-dessous.');
    }

    public function edit(int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $lesson = $module->lessons()->findOrFail($lessonId);

        return view('instructor.lessons.edit', compact('course', 'module', 'lesson'));
    }

    public function update(Request $request, int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $lesson = $module->lessons()->findOrFail($lessonId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_provider' => 'nullable|in:youtube,vimeo,upload',
            'is_free_preview' => 'nullable|boolean',
            'is_downloadable' => 'nullable|boolean',
            'local_video' => 'nullable|file|mimes:mp4,mov,mkv,avi|max:512000', // Max 500MB
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
        ]);

        $validated['is_free_preview'] = $request->has('is_free_preview');
        $validated['is_downloadable'] = $request->has('is_downloadable');

        // Gérer l'upload de vidéo locale
        if ($request->hasFile('local_video')) {
            if ($lesson->video_url && $lesson->video_provider === 'upload') {
                Storage::disk('public')->delete($lesson->video_url);
            }
            $path = $request->file('local_video')->store('lessons/videos', 'public');
            $validated['video_url'] = $path;
            $validated['video_provider'] = 'upload';

            // Simuler la progression du transcodage
            $meta = $lesson->meta_data ?? [];
            $meta['transcoding_status'] = 'processing';
            $meta['transcoding_progress'] = 0;
            $validated['meta_data'] = $meta;
        }

        // Gérer l'upload de fichier PDF de cours
        if ($request->hasFile('pdf_file')) {
            if ($lesson->type === 'pdf' && $lesson->content) {
                Storage::disk('public')->delete($lesson->content);
            }
            $path = $request->file('pdf_file')->store('lessons/documents', 'public');
            $validated['content'] = $path;
        }

        $lesson->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Leçon sauvegardée.']);
        }

        return redirect()->route('instructor.courses.edit', $course->id)->with('success', 'Leçon mise à jour avec succès.');
    }

    public function reorder(Request $request, int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $order = $request->input('order');

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                $module->lessons()->where('id', $id)->update(['sort_order' => $index]);
            }
            return response()->json(['success' => true, 'message' => 'Ordre des leçons mis à jour.']);
        }

        return response()->json(['success' => false, 'message' => 'Données invalides.'], 400);
    }

    public function duplicate(int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $lesson = $module->lessons()->with('resources')->findOrFail($lessonId);

        $newLesson = $lesson->replicate();
        $newLesson->title = $lesson->title . ' - Copie';
        $newLesson->slug = Str::slug($newLesson->title) . '-' . Str::random(4);
        $newLesson->sort_order = $module->lessons()->count();
        $newLesson->save();

        foreach ($lesson->resources as $resource) {
            $newResource = $resource->replicate();
            $newResource->lesson_id = $newLesson->id;
            $newResource->save();
        }

        return back()->with('success', 'Leçon dupliquée avec succès.');
    }

    public function preview(int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $lesson = $module->lessons()->findOrFail($lessonId);

        // Affiche la vue de prévisualisation (simulant le lecteur de l'apprenant)
        return view('instructor.lessons.preview', compact('course', 'module', 'lesson'));
    }

    public function destroy(int $courseId, int $moduleId, int $lessonId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $lesson = $module->lessons()->findOrFail($lessonId);

        // Supprimer les fichiers physiques
        if ($lesson->video_url && $lesson->video_provider === 'upload') {
            Storage::disk('public')->delete($lesson->video_url);
        }
        if ($lesson->type === 'pdf' && $lesson->content) {
            Storage::disk('public')->delete($lesson->content);
        }

        $lesson->delete();

        return back()->with('success', 'Leçon supprimée avec succès.');
    }
}
