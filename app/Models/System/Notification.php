<?php
namespace App\Models\System;

class Notification {
    public int $id;
    public string $title;
    public string $message;
    public string $timestamp;
    public bool $isRead;
    public array $targetRoles;

    public function __construct(array $data) {
        $this->id = $data['NotificationID'] ?? 0;
        $this->title = $data['Title'] ?? '';
        $this->message = $data['Message'] ?? '';
        $this->timestamp = $data['Timestamp'] ?? '';
        $this->isRead = (bool)($data['isRead'] ?? false);
        $this->targetRoles = $data['targetRoles'] ? explode(',', $data['targetRoles']) : [];
    }
}