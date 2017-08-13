<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use Notifiable, EntrustUserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'confirm_code'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($password)
    {
        return $this->attributes['password'] = \Hash::make($password);
    }

    public function likes()
    {
        return $this->belongsToMany(Article::class, 'likes')->withTimestamps();
    }

    //用户点赞一个话题
    public function likeThis($article)
    {
        return $this->likes()->toggle($article);
    }

    //用户是否点赞了这个话题
    public function hasLikedThis($article)
    {
        return $this->likes()->where('article_id', $article)->count();
    }
}
