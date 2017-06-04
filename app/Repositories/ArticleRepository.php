<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/6/4
 * Time: 9:19
 */
namespace App\Repositories;

use Cache;
use App\Article;

class ArticleRepository
{
    public function latestArticle($page)
    {
        return Cache::tags('articles')->remember('articles'.$page, $minutes = 10, function() {
            return Article::with('user','topics')->latest('last_comment_time')->paginate(30);
        });
    }

}