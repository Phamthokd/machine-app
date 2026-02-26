<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditResult extends Model
{
    protected $fillable = [
        'audit_record_id',
        'audit_criterion_id',
        'is_passed',
        'note',
        'image_path',
        'root_cause',
        'corrective_action',
        'improvement_deadline',
    ];

    public function record()
    {
        return $this->belongsTo(AuditRecord::class, 'audit_record_id');
    }

    public function criterion()
    {
        return $this->belongsTo(AuditCriterion::class, 'audit_criterion_id');
    }
}
