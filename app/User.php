<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Cache;

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
    protected $hidden = ['password', 'remember_token'];
    protected $appends = ['isOnLine'];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers() // 关注其他人
    {
        return $this->belongsToMany(self::class, 'followers', 'follower_id', 'followed_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followersUser() // 被其他人关注
    {
        return $this->belongsToMany(self::class, 'followers', 'followed_id', 'follower_id')->withTimestamps();
    }

    /**
     * @param $user
     * @return array
     */
    public function followThisUser($user)
    {
        return $this->followers()->toggle($user);
    }

    public function recordLastActivedAt()
    {
        // 这个 Redis 用于数据库更新，数据库每同步一次则清空一次该 Redis 。
        $data = Cache::get('actived_time_for_update');
        $data[$this->id] = Carbon::now()->toDateTimeString();
        Cache::forever('actived_time_for_update', $data);

        // 这个 Redis 用于读取，每次要获取活跃时间时，先到该 Redis 中获取数据。
        $data = Cache::get('actived_time');
        $data[$this->id] = Carbon::now()->toDateTimeString();
        Cache::forever('actived_time', $data);
    }

    public function lastActivedTime()
    {
        $data = Cache::get('actived_time');
        if(empty($data[$this->id])){
            $data[$this->id] = $this->last_actived_at;
        }
        return $data[$this->id];
    }

    public function getIsOnLineAttribute()
    {
        $key = Redis::hExists('USERS', $this->id);
        return $key;
    }
}
