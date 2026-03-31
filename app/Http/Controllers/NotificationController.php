<?php

namespace App\Http\Controllers;

use App\Models\System\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the current user and their role.
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([]);
        }

        $notifications = Notification::unread()
            ->where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere('target_role', $user->Role)
                      ->orWhere(function($q) {
                          $q->whereNull('user_id')->whereNull('target_role');
                      });
            })
            ->latest()
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function read($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Utility method to create notifications (internal).
     */
    public static function create($title, $message, $link = null, $role = null, $userId = null, $priority = 'Normal')
    {
        return Notification::create([
            'user_id' => $userId,
            'target_role' => $role,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'priority' => $priority
        ]);
    }
}
