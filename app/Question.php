<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title', 'body', 'user_id'
    ];

    public function topics()
    {
        $this->belongsToMany(Topic::class)->withTimestamps();
    }

    public function getTopicListAttribute(){
        return $this->topics->lists('id')->all();
    }
}
