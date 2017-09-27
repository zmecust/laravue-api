<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'only' => ['store']
        ]);
    }

    public function index($id)
    {
        $comments = Comment::where('commentable_id', $id)->where('parent_id', 0)->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar');
        }])->get();
        return $this->responseSuccess('OK', $comments);
    }

    public function childComments($id)
    {
        $parent_id = Request('parent_id');
        $new_comments = [];
        $this->getChildComments($id, $parent_id, $new_comments);
        return $this->responseSuccess('查询成功', $new_comments);
    }

    protected function getChildComments($id, $parent_id, &$new_comments)
    {
        $comments = Comment::where('commentable_id', $id)->where('parent_id', $parent_id)
            ->with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->get();

        if (! empty($comments)) {
            foreach ($comments as $comment) {
                $parent = Comment::where('id', $comment['parent_id'])->first()->user()->first();
                $comment['parent_name'] = $parent->name;
                $comment['parent_user_id'] = $parent->id;
                $new_comments[] = $comment;
                $this->getChildComments($id, $comment['id'], $new_comments);
            }
        }
    }

    public function store()
    {
        $user = Auth::user();
        $comment = Comment::create([
            'commentable_id' => request('article_id'),
            'commentable_type' => 'App\Article',
            'user_id' => $user->id,
            'parent_id' => request('parent_id'),
            'body' => request('body'),
        ]);
        if (! empty($comment)) {
            $user->increment('comments_count');
            $article = Article::where('id', request('article_id'));
            $article->increment('comments_count');
            $article->update([
                'last_comment_user_id' => $user->id,
                'last_comment_time' => Carbon::now(),
            ]);

            $comment = Comment::where('id', $comment->id)->with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])->first();

            if ($comment->parent_id !== 0) {
                $parent = Comment::where('id', $comment->parent_id)->first()->user()->first();
                $comment->parent_name = $parent->name;
                $comment->parent_user_id = $parent->id;
            }
            return $this->responseSuccess('OK', $comment);
        }
        return $this->responseError('Has Something Wrong');
    }
}
