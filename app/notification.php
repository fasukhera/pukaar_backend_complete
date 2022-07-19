<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    protected $fillable = [
        'sender_id',
        'reciever_id',
        'data',
        'read_at',
        'created_at',
        'updated_at'
    ];
    public function sender()
    {
        return $this->belongsTo(User::class,'sender_id');
    }
}
