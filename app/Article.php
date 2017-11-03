<?php

namespace App;

use Nicolaslopezj\Searchable\SearchableTrait;

class Article extends BaseModel
{
    use SearchableTrait;

    protected $fillable = [
        'title', 'body', 'user_id', 'article_url'
    ];

    protected $searchable = [
        'columns' => [
            'articles.title' => 10,
            'articles.body'  => 5,
        ]
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    //帖子没有被隐藏
    public function scopeNotHidden($query)
    {
        return $query->where('is_hidden', 'F');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * 获取这篇文章的评论以 parent_id 来分组
     * @return static
     */
    public function getComments()
    {
        return $this->comments()->with('user')->get()->groupBy('parent_id');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }
}
