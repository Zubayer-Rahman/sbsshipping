<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IouJob extends Model
{
    protected $table = 'iou_job';
    
    protected $fillable = [
        'iou_id',
        'job_id',
    ];

    public $timestamps = true;

    public function iou()
    {
        return $this->belongsTo(Iou::class, 'iou_id');
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}