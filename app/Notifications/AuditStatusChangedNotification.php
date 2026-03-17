<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AuditStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $auditId,
        public string $eventKey,
        public string $title,
        public string $message,
        public array $params = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'audit_id' => $this->auditId,
            'event_key' => $this->eventKey,
            'title' => $this->title,
            'message' => $this->message,
            'params' => $this->params,
            'url' => route('audits.show', $this->auditId),
        ];
    }
}

