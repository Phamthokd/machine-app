<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditRecord extends Model
{
    protected $fillable = [
        'audit_template_id',
        'auditor_id',
        'status',
    ];

    public function template()
    {
        return $this->belongsTo(AuditTemplate::class, 'audit_template_id');
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function results()
    {
        return $this->hasMany(AuditResult::class);
    }

    public function isPaused(): bool
    {
        return $this->status === 'paused';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getTotalCriteriaCountAttribute(): int
    {
        if ($this->relationLoaded('template') && $this->template) {
            return $this->template->relationLoaded('criteria') ? $this->template->criteria->count() : $this->template->criteria()->count();
        }
        return $this->template ? $this->template->criteria()->count() : 0;
    }

    public function getAnsweredCriteriaCountAttribute(): int
    {
        return $this->results->count();
    }

    public function getScoreAttribute()
    {
        $total = $this->results->count();
        if ($total === 0) return 0;
        
        $passed = $this->results->where('is_passed', true)->count();
        return round(($passed / $total) * 100, 2);
    }
}
