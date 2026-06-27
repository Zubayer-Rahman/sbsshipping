<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $fillable = ['staff_id', 'date', 'status', 'note'];
    protected $casts = ['date' => 'date'];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
