<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SevenSChecklist extends Model
{
    protected $fillable = ['department', 'section', 'sort_order', 'content'];

    public function results()
    {
        return $this->hasMany(SevenSResult::class, 'checklist_id');
    }
}
