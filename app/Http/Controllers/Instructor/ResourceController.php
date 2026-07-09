<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function library(Request $request)
    {
        $user = auth()->user();
        $query = Resource::whereHas('lesson.module.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        });

        if ($request->filled('search')) {
            $query->where('file_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('file_type', 'like', $request->type . '%');
        }

        $resources = $query->latest()->paginate(20);

        return view('instructor.resources.library', compact('resources'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'file' => 'required|file|max:102400', // 100MB max
            'title' => 'nullable|string|max:255',
            'is_downloadable' => 'nullable|boolean',
        ]);

        $lesson = Lesson::findOrFail($request->lesson_id);
        
        // Sécuriser l'accès
        if ($lesson->module->course->instructor_id !== auth()->id()) {
            abort(403, 'Accès non autorisé.');
        }

        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('lessons/resources', 'public');
        $fileSize = $file->getSize();
        $fileType = $file->getMimeType();

        $resource = Resource::create([
            'lesson_id' => $lesson->id,
            'title' => $request->input('title') ?? pathinfo($fileName, PATHINFO_FILENAME),
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'type' => $request->input('type') ?? 'document',
            'is_downloadable' => $request->has('is_downloadable'),
            'sort_order' => $lesson->resources()->count(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'resource' => $resource,
                'message' => 'Ressource ajoutée avec succès.'
            ]);
        }

        return back()->with('success', 'Ressource ajoutée avec succès.');
    }

    public function destroy(int $id)
    {
        $user = auth()->user();
        $resource = Resource::whereHas('lesson.module.course', function ($q) use ($user) {
            $q->where('instructor_id', $user->id);
        })->findOrFail($id);

        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();

        return back()->with('success', 'Ressource supprimée avec succès.');
    }
}
