<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'gender', 'dob', 'id_number',
        'education', 'language_skills', 'position_applied',
        'phone', 'address', 'bank_account', 'photo_path',
        'marital_status', 'children_dob', 'referral_source',
        'referral_name', 'referral_department', 'referral_relation',
        'emergency_name', 'emergency_address', 'emergency_relation', 'emergency_phone',
        'work_experiences', 'expected_salary', 'submitted_by',
    ];

    protected $casts = [
        'dob'              => 'date',
        'children_dob'     => 'array',
        'referral_source'  => 'array',
        'work_experiences' => 'array',
    ];

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function seniorManagers()
    {
        return $this->belongsToMany(User::class, 'candidate_senior_manager', 'candidate_id', 'user_id')
                    ->withPivot('review_note', 'reviewed_at', 'review_result')
                    ->withTimestamps();
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender === 'male' ? __('messages.gender_male') : __('messages.gender_female');
    }

    public function getMaritalLabelAttribute(): string
    {
        return match ($this->marital_status) {
            'married'  => __('messages.marital_married'),
            'divorced' => __('messages.marital_divorced'),
            default    => __('messages.marital_single'),
        };
    }
}
