<?php
namespace App\Services;

use App\Models\System\Notification;

class NotificationService {
    private \PDO $db;

    public function __construct(\PDO $db) {
        $this->db = $db;
    }

    public function getNotificationsForRole(string $role, int $limit = 20): array {
        $stmt = $this->db->prepare("
            SELECT * FROM Notifications 
            WHERE targetRoles IS NULL OR targetRoles = '' OR FIND_IN_SET(:role, targetRoles) > 0
            ORDER BY Timestamp DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':role', $role);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Notification($data), $results);
    }

    public function markAllAsRead(int $userId): bool {
        $stmt = $this->db->prepare("UPDATE Notifications SET isRead = 1 WHERE FIND_IN_SET(:role, targetRoles) > 0");
        return $stmt->execute(['role' => $userId]);
    }
}