<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the messages.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Récupérer toutes les discussions
        $sentUsers = Message::where('sender_id', $user->id)->pluck('receiver_id');
        $receivedUsers = Message::where('receiver_id', $user->id)->pluck('sender_id');
        $contactIds = $sentUsers->merge($receivedUsers)->unique();

        // Les contacts sont les formateurs + les utilisateurs déjà contactés
        $contacts = User::whereIn('id', $contactIds)
            ->orWhereHas('roles', function($q) {
                $q->where('name', 'formateur');
            })->get();

        $activeContact = null;
        $messages = collect();

        if ($request->filled('contact_id')) {
            $activeContact = User::findOrFail($request->contact_id);
        } elseif ($contacts->count() > 0) {
            $activeContact = $contacts->first();
        }

        if ($activeContact) {
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

        // Récupérer la liste brute des formateurs pour un éventuel sélecteur
        $instructors = User::whereHas('roles', function($q) {
            $q->where('name', 'formateur');
        })->get();

        return view('learner.messages.index', compact('contacts', 'activeContact', 'messages', 'instructors'));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'body' => 'required|string',
            'subject' => 'nullable|string|max:255',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'subject' => $request->subject ?? 'Message de l\'apprenant',
            'body' => $request->body,
            'is_read' => false,
        ]);

        return back()->with('success', 'Message envoyé avec succès.');
    }
}
