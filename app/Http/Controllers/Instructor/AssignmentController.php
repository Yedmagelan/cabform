<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function store(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:1',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|string',
            'max_file_size_mb' => 'required|integer|min:1',
            'allowed_file_types' => 'nullable|array',
            'max_submissions' => 'required|integer|min:1',
            'rubric' => 'nullable|array',
        ]);

        // Combiner la date et l'heure limite
        $dueDate = null;
        if ($request->filled('due_date')) {
            $dueTime = $request->input('due_time', '23:59');
            $dueDate = date('Y-m-d H:i:s', strtotime($validated['due_date'] . ' ' . $dueTime));
        }

        Assignment::create([
            'course_id' => $course->id,
            'module_id' => $validated['module_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'] ?? null,
            'max_score' => $validated['max_score'],
            'passing_score' => $validated['passing_score'],
            'due_date' => $dueDate,
            'max_file_size_mb' => $validated['max_file_size_mb'],
            'allowed_file_types' => $validated['allowed_file_types'] ?? [],
            'max_submissions' => $validated['max_submissions'],
            'is_active' => true,
            'sort_order' => $course->assignments()->count(),
            'rubric' => $validated['rubric'] ?? [],
        ]);

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Devoir créé avec succès.');
    }

    public function edit(int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        
        $modules = $course->modules;
        $lessons = $course->lessons;

        return view('instructor.assignments.edit', compact('course', 'assignment', 'modules', 'lessons'));
    }

    public function update(Request $request, int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'instructions' => 'nullable|string',
            'module_id' => 'nullable|exists:modules,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'max_score' => 'required|numeric|min:1',
            'passing_score' => 'required|numeric|min:1',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|string',
            'max_file_size_mb' => 'required|integer|min:1',
            'allowed_file_types' => 'nullable|array',
            'max_submissions' => 'required|integer|min:1',
            'rubric' => 'nullable|array',
        ]);

        $dueDate = null;
        if ($request->filled('due_date')) {
            $dueTime = $request->input('due_time', '23:59');
            $dueDate = date('Y-m-d H:i:s', strtotime($validated['due_date'] . ' ' . $dueTime));
        }

        $assignment->update([
            'module_id' => $validated['module_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'instructions' => $validated['instructions'] ?? null,
            'max_score' => $validated['max_score'],
            'passing_score' => $validated['passing_score'],
            'due_date' => $dueDate,
            'max_file_size_mb' => $validated['max_file_size_mb'],
            'allowed_file_types' => $validated['allowed_file_types'] ?? [],
            'max_submissions' => $validated['max_submissions'],
            'rubric' => $validated['rubric'] ?? [],
        ]);

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Devoir mis à jour avec succès.');
    }

    public function duplicate(int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);

        $newAssignment = $assignment->replicate();
        $newAssignment->title = $assignment->title . ' - Copie';
        $newAssignment->sort_order = $course->assignments()->count();
        $newAssignment->save();

        return back()->with('success', 'Devoir dupliqué avec succès.');
    }

    public function destroy(int $courseId, int $assignmentId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $assignment = $course->assignments()->findOrFail($assignmentId);
        $assignment->delete();

        return redirect()->route('instructor.courses.edit', [
            'course' => $course->id,
            'tab' => 'structure'
        ])->with('success', 'Devoir supprimé avec succès.');
    }
}
