<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'name', 'bio', 'questions_count'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class)->withTimestamps();
    }
}