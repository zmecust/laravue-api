<?php

namespace App\Http\Controllers;

use App\Article;
use App\Notifications\LikeArticleNotification;
use Auth;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    //用户是否点赞了这个话题
    public function isLike(Request $request)
    {
        $user =  Auth::user();
        $liked = $user->hasLikedThis($request->get('id'));
        if ($liked) {
            return $this->responseSuccess('OK', ['liked' => true]);
        }
        return $this->responseSuccess('OK', ['liked' => false]);
    }

    //点赞该话题
    public function likeThisArticle(Request $request)
    {
        $user =  Auth::user();
        $article  = Article::where('id', ($request->get('id')))->first();
        $liked = $user->likeThis($article->id);
        if (count($liked['detached']) > 0) { //如果是取消收藏
            $user->decrement('likes_count');
            $article->decrement('likes_count');
            return $this->responseSuccess('OK', ['liked' => false]);
        }
        $data = [
            'name' => $user->name,
            'user_id' => $user->id,
            'title' => $article->title,
            'title_id' => $article->id
        ];
        $article->user->notify(new LikeArticleNotification($data));
        $user->increment('likes_count');
        $article->increment('likes_count');
        return $this->responseSuccess('OK', ['liked' => true]);
    }
}