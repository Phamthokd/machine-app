<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevenSV2Issue extends Model
{
    protected $table = 'seven_s_v2_issues';

    protected $fillable = [
        'record_id',
        'content',
        'severity',
        'penalty_points',
        'location_detail',
        'image_path',
        'status',
        'improvement_note',
        'improvement_image_path',
        'improver_id',
        'improved_at',
        'verifier_id',
        'verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'image_path' => 'array',
        'improvement_image_path' => 'array',
        'improved_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function record()
    {
        return $this->belongsTo(SevenSV2Record::class, 'record_id');
    }

    public function improver()
    {
        return $this->belongsTo(User::class, 'improver_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function isLight(): bool
    {
        return $this->severity === 'light';
    }

    public function isHeavy(): bool
    {
        return $this->severity === 'heavy';
    }

    public function getSeverityTextAttribute(): string
    {
        return $this->severity === 'heavy' ? __('messages.7s_v2_heavy_fault_score') : __('messages.7s_v2_light_fault_score');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'improved' => ['text' => __('messages.7s_v2_status_improved'), 'class' => 'bg-info text-white'],
            'verified' => ['text' => __('messages.7s_v2_status_verified'), 'class' => 'bg-success text-white'],
            'rejected' => ['text' => __('messages.7s_v2_status_rejected'), 'class' => 'bg-danger text-white'],
            default    => ['text' => __('messages.7s_v2_status_pending'), 'class' => 'bg-warning text-dark'],
        };
    }
}
