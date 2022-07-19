<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model
{
    protected $hidden=[
        'sender_id',
        'message_id',
        'type',
        'seen_status',
        'deliver_status',
        'created_at',
        'updated_at'
    ];
    public function message() {
        return $this->belongsTo(Message::class);
    }

    public function message_group() {
        return $this->belongsTo(MessageGroup::class);
    }

    public function messages()
    {
        return $this->belongsTo(Message::class,'message_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'reciever_id');
    }

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }
}
