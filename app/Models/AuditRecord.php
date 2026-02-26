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

    public function getScoreAttribute()
    {
        $total = $this->results->count();
        if ($total === 0) return 0;
        
        $passed = $this->results->where('is_passed', true)->count();
        return round(($passed / $total) * 100, 2);
    }
}
