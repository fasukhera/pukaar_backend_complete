<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OneOnOneSession extends Model
{
    protected $fillable = [
        'client_id',
        'therapist_id',
        'session_require_time',
        'data',
        'status',
    ];

    protected $hidden = [
        'status',
        'taken',
        'created_at',
        'updated_at'
    ];

    public function client()
    {
        return $this->belongsTo(User::class,'client_id');
    }

    public function therapist()
    {
        return $this->belongsTo(User::class,'therapist_id');
    }
}
