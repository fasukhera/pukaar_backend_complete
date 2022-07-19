<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bio extends Model
{
    protected $fillable = [
        'address',
        'country',
        'state',
        'city',
        'user_id',
        'picture',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
