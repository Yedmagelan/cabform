<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = $user->notifications();

        if ($request->filled('type')) {
            if ($request->type === 'unread') {
                $query->unread();
            } elseif ($request->type === 'read') {
                $query->read();
            }
        }

        $notifications = $query->paginate(20);

        return view('instructor.notifications', compact('notifications'));
    }

    public function markAsRead(string $id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notification marquée comme lue.');
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function destroy(string $id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    public function saveSettings(Request $request)
    {
        $user = auth()->user();
        $profile = $user->profile ?? $user->profile()->create();

        $interests = $profile->interests ?? [];
        $interests['notification_preferences'] = [
            'submission' => $request->has('notif_submission'),
            'forum' => $request->has('notif_forum'),
            'message' => $request->has('notif_message'),
            'inactive' => $request->has('notif_inactive'),
            'cert' => $request->has('notif_cert'),
            'frequency' => $request->input('notif_frequency', 'immediate'), // immediate, daily, weekly
            'channels' => $request->input('notif_channels', ['email', 'in_app']),
        ];

        $profile->update([
            'interests' => $interests,
        ]);

        return back()->with('success', 'Préférences de notification enregistrées.');
    }
}
