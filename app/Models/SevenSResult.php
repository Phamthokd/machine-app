<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevenSResult extends Model
{
    protected $fillable = [
        'record_id',
        'checklist_id',
        'grade',
        'note',
        'image_path',
        'points',
        'improvement_note',
        'improvement_image_path',
        'improver_id',
        'improved_at',
        'review_status',
        'reviewer_id',
        'review_note',
        'reviewed_at'
    ];

    protected $casts = [
        'image_path' => 'array',
        'improvement_image_path' => 'array',
        'improved_at' => 'datetime',
    ];

    public function record()
    {
        return $this->belongsTo(SevenSRecord::class, 'record_id');
    }

    public function checklist()
    {
        return $this->belongsTo(SevenSChecklist::class, 'checklist_id');
    }

    public function improver()
    {
        return $this->belongsTo(User::class, 'improver_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public static function gradeToPoints(string $grade): int
    {
        return match ($grade) {
            'B' => 2,
            'C' => 1,
            'D' => 0,
            'E' => -5,
            default => 0,
        };
    }
}
