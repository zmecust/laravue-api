<?php
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/7/22
 * Time: 15:35
 */
namespace App\Repositories;

use App\Tag;
use Cache;
use App\Article;

class ArticleRepository
{
    public function getArticles($page, $request)
    {
        if (empty($request->tag)) {
            return Cache::tags('articles')->remember('articles' . $page, $minutes = 10, function() {
                return Article::notHidden()->with('user', 'tags')->latest('last_comment_time')->paginate(30);
            });
        } else {
            return Cache::tags('articles')->remember('articles' . $page . $request->tag, $minutes = 10, function() use ($request) {
                return Article::notHidden()->whereHas('tags', function ($query) use ($request) {
                    $query->where('name', $request->tag);
                })->with('user', 'tags')->latest('last_comment_time')->paginate(30);
            });
        }
    }

    public function getArticle($id)
    {
        $article = Article::where('id', $id);
        $article->increment('view_count', 1);
        return $article->with('user', 'tags' ,'comments')->first();
    }

    public function byId($id)
    {
        return Article::find($id);
    }

    public function normalizeTopics($tags)
    {
        return collect($tags)->map(function ($tag) {
            if (is_numeric($tag)) {
                Tag::find($tag)->increment('articles_count');
                return (int)$tag;
            }
            $newTag = Tag::create(['name' => $tag, 'articles_count' => 1]);
            return $newTag->id;
        })->toArray();
    }

    public function create(array $attributes)
    {
        return Article::create($attributes);
    }

}