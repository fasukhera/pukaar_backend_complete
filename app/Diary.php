<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Diary extends Model
{
    protected $fillable = [
        'mood',
        'anxiety',
        'energy',
        'self_confidence',
        'feeling',
        'client_profile_id',
        'status'
    ];

    protected $hidden = [
        'client_profile_id',
    ];

    public function profile()
    {
        return $this->belongsTo(ClientProfile::class,'client_profile_id');
    }
}
