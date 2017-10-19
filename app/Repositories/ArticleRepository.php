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
                return Article::notHidden()->with('user', 'tags', 'category')->latest('created_at')->paginate(15);
            });
        } else {
            return Cache::tags('articles')->remember('articles' . $page . $request->tag, $minutes = 10, function() use ($request) {
                return Article::notHidden()->whereHas('tags', function ($query) use ($request) {
                    $query->where('name', $request->tag);
                })->with('user', 'tags', 'category')->latest('created_at')->paginate(15);
            });
        }
    }

    public function getArticle($id)
    {
        $article = Article::where('id', $id);
        $article->increment('view_count', 1);
        return $article->with('user', 'tags')->first();
    }

    public function byId($id)
    {
        return Article::find($id);
    }

    public function createTopics($tags)
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

    public function editTopics($tags, $id)
    {
        $oldTags = Article::find($id)->tags->pluck('id')->toArray();
        $reduceTags = array_diff($oldTags, $tags);
        $addTags = array_diff($tags, $oldTags);

        foreach($reduceTags as $reduceTag) {
            $tag = Tag::where('id', $reduceTag);
            $tagCount = $tag->count();
            if ($tagCount > 1) {
                \DB::table('article_tag')->where('tag_id', $reduceTag)->where('article_id', $id)->delete();
                $tag->decrement('count', 1);
            } else {
                $tag->delete();
            }
        }

        if (! is_null($addTags)) {
            return $addTags;
        } else {
            return false;
        }
    }

    public function create(array $attributes)
    {
        return Article::create($attributes);
    }

}
