<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name', 'gender', 'dob', 'id_number',
        'education', 'language_skills', 'department_applied', 'position_applied',
        'phone', 'address', 'bank_account', 'photo_path',
        'marital_status', 'children_dob', 'referral_source',
        'referral_name', 'referral_department', 'referral_relation',
        'emergency_name', 'emergency_address', 'emergency_relation', 'emergency_phone',
        'work_experiences', 'expected_salary', 'submitted_by',
        'hr_notes', 'hr_photo_path',
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
                    ->withPivot('review_note', 'reviewed_at', 'review_result',
                                'proposed_salary', 'start_date', 'probation_period',
                                'assigned_department', 'extra_note', 'is_locked')
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

    public function getOverallReviewStatusAttribute(): string
    {
        $managers = $this->seniorManagers;
        if ($managers->isEmpty()) {
            return 'new';
        }

        $approvedManager = $managers->first(fn($u) => $u->pivot->review_result === 'approved');
        if ($approvedManager) {
            return $approvedManager->pivot->is_locked ? 'approved_locked' : 'approved_draft';
        }

        $rejectedManager = $managers->first(fn($u) => $u->pivot->review_result === 'rejected');
        if ($rejectedManager) {
            return $rejectedManager->pivot->is_locked ? 'rejected_locked' : 'rejected_draft';
        }

        if ($managers->count() > 1) {
            return 'forwarded';
        }

        return 'routed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->overall_review_status) {
            'new'             => __('messages.candidate_status_new'),
            'routed'          => __('messages.candidate_status_routed'),
            'forwarded'       => __('messages.candidate_status_forwarded'),
            'approved_locked' => __('messages.candidate_status_approved_locked'),
            'approved_draft'  => __('messages.candidate_status_approved_draft'),
            'rejected_locked' => __('messages.candidate_status_rejected_locked'),
            'rejected_draft'  => __('messages.candidate_status_rejected_draft'),
            'approved'        => __('messages.candidate_status_approved'),
            'rejected'        => __('messages.candidate_status_rejected'),
            default           => __('messages.candidate_status_new'),
        };
    }
}
