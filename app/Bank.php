<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'bank_name',
        'branch_name',
        'account_number',
        'account_title',
        'iban'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];
}
