<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnvironmentReportEntry extends Model
{
    protected $fillable = [
        'environment_report_id',
        'report_date',
        'day_number',
        'humidity_0730',
        'humidity_1030',
        'humidity_1400',
        'humidity_1630',
        'temperature_0730',
        'temperature_1030',
        'temperature_1400',
        'temperature_1630',
        'weather',
        'action_0730',
        'action_1030',
        'action_1400',
        'action_1630',
        'checked_by',
    ];

    protected $casts = [
        'report_date' => 'date',
    ];

    public function report()
    {
        return $this->belongsTo(EnvironmentReport::class, 'environment_report_id');
    }
}
