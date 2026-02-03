<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'code',
        'type',
    ];

    public function machines()
    {
        return $this->hasMany(Machine::class, 'current_department_id');
    }
}
