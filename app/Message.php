<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['from_uid', 'to_uid', 'content', 'is_read', 'dialog_id'];

    //发送私信的用户
    public  function fromUser()
    {
        return $this->belongsTo(User::class, 'from_uid');
    }

    //接受私信的用户
    public  function toUser()
    {
        return  $this->belongsTo(User::class, 'to_uid');
    }
}
