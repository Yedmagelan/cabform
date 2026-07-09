<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\ForumThread;
use App\Models\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $threads = $course->forumThreads()->with(['user', 'posts'])->latest()->paginate(15);

        return view('instructor.courses.forum', compact('course', 'threads'));
    }

    public function show(int $courseId, int $threadId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->with(['user', 'posts.user'])->findOrFail($threadId);

        return view('instructor.courses.forum-thread', compact('course', 'thread'));
    }

    public function pin(int $courseId, int $threadId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->findOrFail($threadId);

        $thread->update(['is_pinned' => !$thread->is_pinned]);

        return back()->with('success', $thread->is_pinned ? 'Sujet épinglé.' : 'Sujet désépinglé.');
    }

    public function lock(int $courseId, int $threadId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->findOrFail($threadId);

        $thread->update(['is_locked' => !$thread->is_locked]);

        return back()->with('success', $thread->is_locked ? 'Sujet verrouillé.' : 'Sujet déverrouillé.');
    }

    public function reply(Request $request, int $courseId, int $threadId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->findOrFail($threadId);

        $request->validate([
            'body' => 'required|string',
        ]);

        ForumPost::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);

        $thread->increment('replies_count');
        $thread->update(['last_reply_at' => now()]);

        return back()->with('success', 'Votre réponse a été publiée.');
    }

    public function destroyThread(int $courseId, int $threadId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->findOrFail($threadId);
        $thread->delete();

        return redirect()->route('instructor.forum.index', $course->id)->with('success', 'Discussion supprimée.');
    }

    public function destroyPost(int $courseId, int $threadId, int $postId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $thread = $course->forumThreads()->findOrFail($threadId);
        $post = ForumPost::where('thread_id', $thread->id)->findOrFail($postId);
        
        $post->delete();
        $thread->decrement('replies_count');

        return back()->with('success', 'Message supprimé.');
    }
}
