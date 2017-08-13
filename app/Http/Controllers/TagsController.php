<?php

namespace App\Http\Controllers;

use Cache;
use App\Tag;
use DB;

class TagsController extends Controller
{
    public function index()
    {
        if (empty($tags = Cache::get('Tags_cache'))) {
            $tags = DB::table('tags')->select('id', 'name')->get();;
            Cache::put('Tags_cache', $tags, 10);
        }
        return $this->responseSuccess('查询成功', $tags);
    }

    public function hotTags()
    {
        if (empty($hotTags = Cache::get('hotTags_cache'))) {
            $hotTags = Tag::where([])->orderBy('articles_count', 'desc')->take(20)->get();
            Cache::put('hotTags_cache', $hotTags, 10);
        }
        return $this->responseSuccess('查询成功', $hotTags);
    }
}