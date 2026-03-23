<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SevenSStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $recordId,
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
            'record_id' => $this->recordId,
            'event_key' => $this->eventKey,
            'title' => $this->title,
            'message' => $this->message,
            'params' => $this->params,
            'url' => route('seven-s.show', $this->recordId),
        ];
    }
}
