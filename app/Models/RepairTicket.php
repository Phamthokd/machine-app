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
        'type', // mechanic, contractor
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



}

