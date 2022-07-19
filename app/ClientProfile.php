<?php

namespace App;

use App\Http\Controllers\Api\Diary\DiaryController;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    protected $fillable = [
        'orientation',
        'religion',
        'religion_identifier',
        'medicines',
        'sleeping_habit',
        'problem',
        'user_id',
        'therapist_id',
        'check_assigned',
        'change_therapist',
        'change_therapist_reason',
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

    public function diary()
    {
        return $this->hasMany(Diary::class);
    }

    public function therapist()
    {
        return $this->belongsTo(TherapistProfile::class,'therapist_profile_id');
    }

}
