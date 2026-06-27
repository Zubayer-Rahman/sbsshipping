<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'group_code',
        'description',
        'status',
        'created_by',
    ];

    public function jobs()
    {
        return $this->belongsToMany(
            Job::class,
            'job_group_job',
            'job_group_id',
            'job_id',
            'id',
            'id'
        );
    }

    public function Job(){
        return $this->belongsTo(Job::class, 'job_id', 'imp_exp_value');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateGroupCode()
    {
        $last = self::latest('id')->first();
        $number = $last ? $last->id + 1 : 1;
        return 'GRP-' . date('Y') . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
