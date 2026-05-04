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
            'title'      => 'Phiếu sửa hoàn thành – Cần đánh giá',
            'message'    => "Phiếu #{$this->ticket->code} – {$this->ticket->machine->ma_thiet_bi} đã sửa xong. Vui lòng đánh giá chất lượng sửa chữa.",
            'url'        => "/repairs/{$this->ticket->id}/evaluate",
        ];
    }
}
