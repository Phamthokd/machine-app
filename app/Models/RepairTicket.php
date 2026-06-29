<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairTicket extends Model
{
    protected $fillable = [
        'code',
        'department_id',
        'machine_id',
        'ma_hang',
        'cong_doan',
        'mo_ta_loi',
        'nguyen_nhan',
        'noi_dung_sua_chua',
        'started_at',
        'ended_at',
        'endline_qc_name',
        'inline_qc_name',
        'qa_supervisor_name',
        'created_by',
        'status',
        'nguoi_ho_tro',
        'type', // mechanic, contractor, bok
        'mechanic_id',
        'eval_response_time',
        'eval_repair_speed',
        'eval_error_rate',
        'evaluated_at',
        // Approval workflow
        'approval_status', // null | pending_approval | approved | rejected
        'approval_note',
        'approved_by',
        'approved_at',
        'images',
    ];

    protected $casts = [
        'evaluated_at' => 'datetime',
        'approved_at'  => 'datetime',
        'images'       => 'array',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === 'pending_approval';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved' || $this->approval_status === null;
    }
}

