<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevenSRecord extends Model
{
    protected $fillable = ['department', 'inspector_id', 'score', 'max_score', 'status'];

    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function results()
    {
        return $this->hasMany(SevenSResult::class, 'record_id');
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed' || empty($this->status);
    }

    public function getTotalChecklistItemsAttribute(): int
    {
        return SevenSChecklist::where('department', $this->department)->count();
    }

    public function getAnsweredItemsCountAttribute(): int
    {
        return $this->results()->count();
    }

    public function getScorePercentAttribute(): float
    {
        return $this->max_score > 0
            ? round(($this->score / $this->max_score) * 100, 1)
            : 0;
    }
}
