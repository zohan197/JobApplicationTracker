<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobListing extends Model
{
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    use HasFactory;

    protected $fillable = [
        'user_id',
        'company',
        'position',
        'status',
        'url',
        'applied_at',
    ];
}
