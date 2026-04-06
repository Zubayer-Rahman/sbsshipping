<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForwardingLetter extends Model
{
    protected $fillable = [
        'ref_no', 'letter_date', 'subject', 'contact_id',
        'selected_job_ids', 'visible_columns', 'bank_details',
        'total_amount', 'user_id',
    ];

    protected $casts = [
        'letter_date'     => 'date',
        'selected_job_ids'=> 'array',
        'visible_columns' => 'array',
        'total_amount'    => 'decimal:2',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get the actual job records for this letter
    public function jobs()
    {
        $ids = $this->selected_job_ids ?? [];
        if (empty($ids)) return collect();
        return \DB::table('sbs_jobs')->whereIn('id', $ids)->get();
    }
}