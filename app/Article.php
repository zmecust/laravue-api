<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'title', 'body', 'user_id', 'image_url', 'close_comment'
    ];

    public function topics()
    {
        return $this->belongsToMany(Topic::class)->withTimestamps();
    }

    public function getTopicListAttribute(){
        return $this->topics()->lists('id')->all();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isHidden()
    {
        return $this->is_hidden === 'T';
    }
}
