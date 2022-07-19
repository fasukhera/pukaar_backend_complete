<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TherapistProfile extends Model
{
    protected $fillable = [
        'about',
        'city',
        'therapist_focus',
        'type_of_doctor',
        'introduction',
        'education',
        'user_id'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function credentials()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function diary()
    {
        return $this->hasMany(Diary::class);
    }

    public function client()
    {
        return $this->hasMany(ClientProfile::class);
    }

    public function therapist_client_diary()
    {
        return $this->hasMany(TherapistDiary::class);
    }
}
