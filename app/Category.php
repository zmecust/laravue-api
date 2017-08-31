<?php

namespace App;

class Category extends BaseModel
{
    protected $fillable = ['name'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
