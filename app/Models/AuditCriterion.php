<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditCriterion extends Model
{
    protected $fillable = [
        'audit_template_id',
        'content',
        'order_num',
    ];

    public function template()
    {
        return $this->belongsTo(AuditTemplate::class, 'audit_template_id');
    }
}
