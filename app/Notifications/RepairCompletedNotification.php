<?php

namespace App\Notifications;

use App\Models\RepairTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RepairCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(public RepairTicket $ticket) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'repair_id'  => $this->ticket->id,
            'event_key'  => 'repair_completed_needs_evaluation',
            'title'      => 'messages.notif_repair_completed_title',
            'message'    => 'messages.notif_repair_completed_message',
            'params'     => [
                'code'   => $this->ticket->code,
                'device' => $this->ticket->machine->ma_thiet_bi ?? '',
            ],
            'url'        => "/repairs/{$this->ticket->id}/evaluate",
        ];
    }
}
