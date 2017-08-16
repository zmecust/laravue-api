<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\User;
use Cache;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function show($id)
    {
        if (empty($user = Cache::get('users_cache' . $id))) {
            $user = User::findOrFail($id);
            Cache::put('users_cache' . $id, $user, 10);
        }
        return $this->responseSuccess('查询成功', $user);
    }

    public function userArticles($id)
    {
        if (empty($articles = Cache::get('user_articles' . $id))) {
            $articles = Article::where('user_id', $id)->latest('created_at')->get();
            Cache::put('user_articles' . $id, $articles, 10);
        }
        return $this->responseSuccess('查询成功', $articles);
    }

    public function userReplies($id)
    {
        if (empty($comments = Cache::get('user_replies' . $id))) {
            $comments = Comment::where('user_id', $id)->with('article')->latest('created_at')->get();
            Cache::put('user_replies' . $id, $comments, 10);
        }
        return $this->responseSuccess('查询成功', $comments);
    }
}
