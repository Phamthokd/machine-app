<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ItRepair extends Model
{
    protected $table = 'it_repairs';

    protected $fillable = [
        'code',
        'department',
        'reporter_id',
        'machine_id',
        'issue_type',
        'title',
        'description',
        'location',
        'priority',
        'status',
        'resolver_id',
        'resolution_note',
        'resolved_at',
        'images',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'images'      => 'array',
    ];

    // ─── Relationships ───────────────────────────────────────────────────────

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function machine()
    {
        return $this->belongsTo(\App\Models\Machine::class, 'machine_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolver_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function generateCode(): string
    {
        $prefix = 'IT-' . now()->format('Ymd');
        $last   = static::where('code', 'like', $prefix . '-%')
            ->orderByDesc('id')->first();

        $seq = $last ? ((int) substr($last->code, -3)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isResolved(): bool  { return in_array($this->status, ['resolved', 'closed']); }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending'     => 'Chờ xử lý',
            'in_progress' => 'Đang xử lý',
            'resolved'    => 'Đã giải quyết',
            'closed'      => 'Đã đóng',
            default       => $this->status,
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'     => 'warning',
            'in_progress' => 'primary',
            'resolved'    => 'success',
            'closed'      => 'secondary',
            default       => 'secondary',
        };
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'low'    => 'Thấp',
            'medium' => 'Bình thường',
            'high'   => 'Cao',
            'urgent' => 'Khẩn cấp',
            default  => $this->priority,
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'low'    => 'secondary',
            'medium' => 'info',
            'high'   => 'warning',
            'urgent' => 'danger',
            default  => 'secondary',
        };
    }

    public function issueTypeLabel(): string
    {
        return match ($this->issue_type) {
            'computer' => 'Máy tính',
            'network'  => 'Mạng / Internet',
            'printer'  => 'Máy in',
            'software' => 'Phần mềm',
            'other'    => 'Khác',
            default    => $this->issue_type,
        };
    }
}
