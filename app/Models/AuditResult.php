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
        'improver_name',
        'reviewer_name',
        'review_note',
        'review_image_path',
        'reviewed_at',
        'department_agreement',
        'department_reject_reason',
        'audit_rejection_decision',
        'is_completed',
        'completed_at',
        'completion_image_path',
        'completion_note',
    ];
    protected $casts = [
        'is_passed' => 'boolean',
        'department_agreement' => 'boolean',
        'audit_rejection_decision' => 'boolean',
        'is_completed' => 'boolean',
        'image_path' => 'array',
        'completion_image_path' => 'array',
        'completed_at' => 'datetime',
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
