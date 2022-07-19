<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TherapistDiary extends Model
{
    protected $fillable = [
        'mood',
        'anxiety',
        'energy',
        'self_confidence',
        'feeling',
        'client_profile_id',
        'therapist_profile_id'
    ];

    protected $hidden = [
        'client_profile_id',
    ];

    public function client_profile()
    {
        return $this->belongsTo(ClientProfile::class, 'client_profile_id');
    }

    public function therapist_profile()
    {
        return $this->belongsTo(TherapistProfile::class, 'therapist_profile_id');
    }
}
