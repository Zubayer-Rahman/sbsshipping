<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryRecord extends Model
{
    protected $table = 'salary_records';
    protected $fillable = ['staff_id', 'year', 'month', 'advance_cut', 'remarks'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}