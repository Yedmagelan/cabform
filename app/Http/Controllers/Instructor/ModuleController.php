<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    public function store(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        $validated['course_id'] = $course->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(4);
        $validated['sort_order'] = $course->modules()->count();
        $validated['is_active'] = true;
        $validated['duration_minutes'] = $validated['duration_minutes'] ?? 0;

        Module::create($validated);

        return back()->with('success', 'Module créé avec succès.');
    }

    public function update(Request $request, int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'nullable|integer|min:0',
            'unlock_conditions_type' => 'nullable|string',
            'unlock_conditions_value' => 'nullable',
        ]);

        // Gérer les conditions de déblocage
        $unlockConditions = null;
        if ($request->filled('unlock_conditions_type')) {
            $unlockConditions = [
                'type' => $request->unlock_conditions_type,
                'value' => $request->unlock_conditions_value,
            ];
        }

        $module->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration_minutes' => $validated['duration_minutes'] ?? 0,
            'unlock_conditions' => $unlockConditions,
        ]);

        return back()->with('success', 'Module mis à jour avec succès.');
    }

    public function reorder(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $order = $request->input('order'); // Array of module IDs in order

        if (is_array($order)) {
            foreach ($order as $index => $id) {
                $course->modules()->where('id', $id)->update(['sort_order' => $index]);
            }
            return response()->json(['success' => true, 'message' => 'Ordre mis à jour.']);
        }

        return response()->json(['success' => false, 'message' => 'Données invalides.'], 400);
    }

    public function duplicate(int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->with('lessons.resources')->findOrFail($moduleId);

        $newModule = $module->replicate();
        $newModule->title = $module->title . ' - Copie';
        $newModule->slug = Str::slug($newModule->title) . '-' . Str::random(4);
        $newModule->sort_order = $course->modules()->count();
        $newModule->save();

        foreach ($module->lessons as $lesson) {
            $newLesson = $lesson->replicate();
            $newLesson->module_id = $newModule->id;
            $newLesson->slug = Str::slug($newLesson->title) . '-' . Str::random(4);
            $newLesson->save();

            foreach ($lesson->resources as $resource) {
                $newResource = $resource->replicate();
                $newResource->lesson_id = $newLesson->id;
                $newResource->save();
            }
        }

        return back()->with('success', 'Module dupliqué avec succès.');
    }

    public function destroy(int $courseId, int $moduleId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $module = $course->modules()->findOrFail($moduleId);
        $module->delete();

        return back()->with('success', 'Module supprimé avec succès.');
    }
}
