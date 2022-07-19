<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TherapistService extends Model
{
    protected $fillable = [
        'service',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
