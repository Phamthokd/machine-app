<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineMovement extends Model
{
    protected $fillable = [
        'machine_id',
        'from_department_id',
        'to_department_id',
        'user_id',
        'note',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
