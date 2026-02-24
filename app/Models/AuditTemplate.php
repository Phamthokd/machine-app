<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTemplate extends Model
{
    protected $fillable = [
        'name',
        'department_name',
        'is_active',
    ];

    public function criteria()
    {
        return $this->hasMany(AuditCriterion::class)->orderBy('order_num');
    }

    public function records()
    {
        return $this->hasMany(AuditRecord::class);
    }
}
