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
        $comments = $this->getChildComments($id, $parent_id);
        $comments = collect($comments)->map(function ($comment) {
            if (is_array($comment)) {
                $comment = collect($comment)->map(function ($child_comment) {
                    return $child_comment;
                });
            }
            return $comment;
        });
        return $this->responseSuccess('查询成功', $comments);
    }

    protected function getChildComments($id, $parent_id)
    {
        $comments = Comment::where('commentable_id', $id)->where('parent_id', $parent_id)
            ->with(['user' => function ($query) {
            $query->select('id', 'name');
        }])->get();
        $new_comments = [];

        if (! empty($comments)) {
            foreach ($comments as $comment) {
                $comment['parent_name'] = Comment::where('id', $comment['parent_id'])->first()->user()->first()->name;
                $new_comments[] = $comment;
                $comment_child = $this->getChildComments($id, $comment['id']);
                if (! empty($comment_child)) {
                    $new_comments[] = $comment_child;
                }
            }
        }
        return $new_comments;
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
            $comment = $comment->with(['user' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])->get();
            return $this->responseSuccess('OK', $comment);
        }
        return $this->responseError('Has Something Wrong');
    }
}
