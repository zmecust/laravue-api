<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/6/2
 * Time: 21:22
 */
namespace App\Repositories;

use Cache;
use App\Article;

class ArticleRepository
{
    public function latestArticles($page)
    {
        /*$articles = Cache::tags('articles')->remember('articles'.$page, $minutes = 10, function() {
            return Article::with('user','topics')->latest('updated_at')->paginate(30);
        });*/

        return ('hahah');
        $ids = Cache::tags('articles')->remember('articles'.$page, $minutes = 10, function () {
            return Article::all()->latest('updated_at')->pluck('id');
        });
        return $ids->toArray();

        foreach ($ids as $id) {
            // 一级缓存
            yield static::findById($id);
        }
    }

    public static function findById($id)
    {
        return Cache::rememberForever("articles:{$id}", function () use ($id) {
            return Article::where('id', $id)->with('user','topics')->first();
        });
    }
}