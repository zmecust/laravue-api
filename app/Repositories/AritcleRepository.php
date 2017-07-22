<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/7/22
 * Time: 15:35
 */
namespace App\Repositories;

use App\Article;

class ArticleRepository
{
    public function getArticles($page)
    {
        return Cache::tags('articles')->remember('articles' . $page, $minutes = 10, function() {
            return Article::notHidden()->with('user')->withCertain('tags', ['name'])
                ->latest('last_comment_time')->paginate(30);
        });
    }

    public function getArticle($id)
    {
        $article = Article::where('id', $id);
        $article->increment('view_count', 1);
        return $article->with('comments')->first();
    }

    public function byId($id)
    {
        return Article::find($id);
    }
}