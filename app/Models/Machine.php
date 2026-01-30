<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'ma_thiet_bi',
        'ten_thiet_bi',
        'current_department_id',
        'brand',
        'model',
        'serial',
        'invoice_cd',
        'year',
        'country',
        'stock_in_date',
        'vi_tri_text',
        'ngay_vao_kho',
        'ngay_ra_kho',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    public function repairTickets()
    {
        return $this->hasMany(RepairTicket::class);
    }
}
