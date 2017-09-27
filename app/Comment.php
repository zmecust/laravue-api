<?php

namespace App;

class Comment extends BaseModel
{
    protected $fillable = ['user_id', 'body', 'commentable_id', 'commentable_type', 'parent_id'];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function childComments()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

}