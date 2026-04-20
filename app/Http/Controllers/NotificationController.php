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
                $query->where('UserID', '=', $user->UserID)
                      ->orWhere('TargetRole', '=', (string)$user->Role)
                      ->orWhere(function($q) {
                          $q->whereNull('UserID')->whereNull('TargetRole');
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
            $notification->update(['IsRead' => true]);
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
            'UserID' => $userId,
            'TargetRole' => $role,
            'Title' => $title,
            'Message' => $message,
            'Link' => $link,
            'Priority' => $priority
        ]);
    }
}
