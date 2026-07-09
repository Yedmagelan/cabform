<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    /**
     * Display listing of forum threads.
     */
    public function index(Request $request)
    {
        $threadsQuery = ForumThread::with(['user', 'course'])->latest();

        if ($request->has('course_id')) {
            $threadsQuery->where('course_id', $request->course_id);
        }

        $threads = $threadsQuery->paginate(15);
        $courses = Course::published()->get();

        return view('learner.forum.index', compact('threads', 'courses'));
    }

    /**
     * Display a single thread.
     */
    public function show(int $id)
    {
        $thread = ForumThread::with(['user', 'course'])->findOrFail($id);
        $thread->increment('views_count');

        $replies = ForumPost::where('thread_id', $thread->id)
            ->with('user')
            ->oldest()
            ->get();

        return view('learner.forum.show', compact('thread', 'replies'));
    }

    /**
     * Store a new forum thread.
     */
    public function storeThread(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $thread = ForumThread::create([
            'user_id' => auth()->id(),
            'course_id' => $request->course_id,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . time(),
            'body' => $request->body,
            'last_reply_at' => now(),
        ]);

        return redirect()->route('learner.forum.thread.show', $thread->id)->with('success', 'Sujet créé avec succès.');
    }

    /**
     * Store a reply to a thread.
     */
    public function storeReply(Request $request, int $threadId)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $thread = ForumThread::findOrFail($threadId);

        if ($thread->is_locked) {
            return back()->with('error', 'Ce sujet est verrouillé.');
        }

        ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $thread->increment('replies_count');
        $thread->update(['last_reply_at' => now()]);

        return back()->with('success', 'Réponse publiée.');
    }
}
