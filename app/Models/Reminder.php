<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    public function jobListing()
    {
        return $this->belongsTo(JobListing::class);
    }
    protected $fillable = [
        'job_listing_id',
        'remind_at',
        'note',
        'notified',
        'updated_at'
    ];
    protected $casts = [
    'remind_at' => 'datetime',
    'updated_at' => 'datetime',
    ];
}
