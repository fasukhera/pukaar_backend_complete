<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    protected $fillable = [
        'picture',
        'session_status',
        'number_of_sessions',
        'cost',
        'user_id'
    ];

    protected $hidden = [
        'user_id',
//        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
