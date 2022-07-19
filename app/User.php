<?php

namespace App;

use http\Client;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  Notifiable;
    use HasRoles;
    use HasApiTokens;
    public static $guard_name = "web";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'isVerified',
        'password',
        'status_id',
        'passcode',
        'display_picture',
        'reset_passkey'
//        'assigned_client',
//        'assigned_therapist'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'isVerified',
        'status_id',
        'email_verified_at',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function client_profile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function therapist_profile()
    {
        return $this->hasOne(TherapistProfile::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }
//    public function assigned_client()
//    {
//        return $this->belongsTo(User::class,'assigned_client');
//    }
//    public function assigned_therapist()
//    {
//        return $this->belongsTo(User::class,'assigned_therapist');
//    }

//    public function client()
//    {
//        return $this->belongsToMany(ClientProfile::class, 'client_therapists')
//            ->withPivot( 'id','client_id', 'therapist_id' ,'created_at');
//    }
//    public function therapist()
//    {
//        return $this->belongsToMany(TherapistProfile::class, 'client_therapists')
//            ->withPivot( 'id','client_id', 'therapist_id' ,'created_at');
//    }

}
