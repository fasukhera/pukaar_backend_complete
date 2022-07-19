<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'number_of_sessions',
        'price'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

}
