<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\Transformer\CommentTransformer;
use App\User;
use Cache;
use Auth;
use Validator;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    protected $commentTransformer;

    public function __construct(CommentTransformer $commentTransformer)
    {
        $this->commentTransformer = $commentTransformer;
        // 执行 jwt.auth 认证
        $this->middleware('jwt.auth', [
            'only' => ['editPassword']
        ]);
    }

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
            $comments = Comment::where('user_id', $id)->with('commentable')->latest('created_at')->get()->toArray();
            $comments = $this->commentTransformer->transformCollection($comments);
            Cache::put('user_replies' . $id, $comments, 10);
        }
        return $this->responseSuccess('查询成功', $comments);
    }

    public function userLikesArticles($id)
    {
        if (empty($articles = Cache::get('user_likes_articles' . $id))) {
            $articles = Comment::where('user_id', $id)->with('article')->latest('created_at')->get();
            Cache::put('user_likes_articles' . $id, $articles, 10);
        }
        return $this->responseSuccess('查询成功', $articles);
    }

    public function editPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|between:6,16|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->responseError('表单验证失败', $validator->errors()->toArray());
        }

        $user = Auth::user();
        $user->update(['password' => request('password')]);

        return $this->responseSuccess('密码重置成功');
    }
}
