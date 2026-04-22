<?php
namespace App\Services;

use App\Models\System\Notification;
use Illuminate\Support\Facades\DB;

class NotificationService {

    public function getNotificationsForRole(string $role, int $limit = 20): array {
        return Notification::where(function($query) use ($role) {
                $query->whereNull('TargetRole')
                      ->orWhere('TargetRole', '')
                      ->orWhereRaw('FIND_IN_SET(?, TargetRole) > 0', [$role]);
            })
            ->orderBy('CreatedAt', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function markAllAsRead(int $userId): bool {
        return Notification::whereRaw('FIND_IN_SET(?, TargetRole) > 0', [$userId])
            ->update(['IsRead' => 1]);
    }

    public function updateReadStatus(int $notificationId, bool $isRead, $userId): bool {
        return Notification::where('NotificationID', $notificationId)
            ->update(['IsRead' => $isRead ? 1 : 0]);
    }
}
