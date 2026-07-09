<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Récupérer toutes les discussions
        $sentUsers = Message::where('sender_id', $user->id)->pluck('receiver_id');
        $receivedUsers = Message::where('receiver_id', $user->id)->pluck('sender_id');
        $contactIds = $sentUsers->merge($receivedUsers)->unique();

        $contacts = User::whereIn('id', $contactIds)->get();
        
        $activeContact = null;
        $messages = collect();

        if ($request->filled('contact_id')) {
            $activeContact = User::findOrFail($request->contact_id);
            
            $messages = Message::where(function ($q) use ($user, $activeContact) {
                $q->where('sender_id', $user->id)->where('receiver_id', $activeContact->id);
            })->orWhere(function ($q) use ($user, $activeContact) {
                $q->where('sender_id', $activeContact->id)->where('receiver_id', $user->id);
            })->orderBy('created_at', 'asc')->get();

            // Marquer comme lu
            Message::where('sender_id', $activeContact->id)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);
        }

        return view('instructor.messages.index', compact('contacts', 'activeContact', 'messages'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'course_id' => $request->course_id,
            'subject' => 'Message de votre formateur',
            'body' => $request->body,
            'is_read' => false,
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }

    public function announcements(int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $announcements = $course->meta_data['announcements'] ?? [];

        return view('instructor.courses.announcements', compact('course', 'announcements'));
    }

    public function postAnnouncement(Request $request, int $courseId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $meta = $course->meta_data ?? [];
        $announcements = $meta['announcements'] ?? [];

        $newAnnouncement = [
            'id' => uniqid(),
            'title' => $request->title,
            'content' => $request->content,
            'created_at' => now()->toDateTimeString(),
        ];

        array_unshift($announcements, $newAnnouncement); // Mettre la plus récente au début
        $meta['announcements'] = $announcements;

        $course->update(['meta_data' => $meta]);

        // Simuler l'envoi de mail à tous les apprenants inscrits
        // Dans une vraie application, on déclencherait un Job pour cela

        return back()->with('success', 'Annonce publiée avec succès pour tous les apprenants inscrits.');
    }

    public function destroyAnnouncement(int $courseId, string $announcementId)
    {
        $course = auth()->user()->courses()->findOrFail($courseId);
        $meta = $course->meta_data ?? [];
        $announcements = $meta['announcements'] ?? [];

        $announcements = array_filter($announcements, function ($a) use ($announcementId) {
            return $a['id'] !== $announcementId;
        });

        $meta['announcements'] = array_values($announcements);
        $course->update(['meta_data' => $meta]);

        return back()->with('success', 'Annonce supprimée avec succès.');
    }
}
