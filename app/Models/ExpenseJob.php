<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseJob extends Model
{
    protected $table = 'expense_job';
    
    protected $fillable = [
        'expense_id',
        'job_id',
    ];

    public $timestamps = true;

    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }


    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}