<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevenSV2Record extends Model
{
    protected $table = 'seven_s_v2_records';

    protected $fillable = [
        'department',
        'inspector_id',
        'light_fault_count',
        'heavy_fault_count',
        'total_penalty_points',
        'general_note',
        'status',
    ];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function issues()
    {
        return $this->hasMany(SevenSV2Issue::class, 'record_id');
    }

    public function getPendingIssuesCountAttribute(): int
    {
        return $this->issues->whereIn('status', ['pending', 'rejected'])->count();
    }

    public function getImprovedIssuesCountAttribute(): int
    {
        return $this->issues->where('status', 'improved')->count();
    }

    public function getVerifiedIssuesCountAttribute(): int
    {
        return $this->issues->where('status', 'verified')->count();
    }

    public function isAllVerified(): bool
    {
        if ($this->issues->isEmpty()) {
            return true;
        }
        return $this->issues->every(fn($i) => $i->status === 'verified');
    }
}
