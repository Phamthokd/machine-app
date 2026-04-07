<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvironmentReport extends Model
{
    protected $fillable = [
        'department_name',
        'report_year',
        'report_month',
        'creator_id',
        'status',
        'note',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function entries()
    {
        return $this->hasMany(EnvironmentReportEntry::class)->orderBy('day_number');
    }

    public function getPeriodLabelAttribute(): string
    {
        return sprintf('%02d/%04d', $this->report_month, $this->report_year);
    }
}
