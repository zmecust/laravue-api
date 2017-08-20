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
        /*$comments = Comment::where('commentable_id', $id)->where('parent_id', $parent_id)->with('user')->get()->toArray();
        $new_comments = [];

        if (! empty($comments)) {
            foreach ($comments as $comment) {
                if ($comment['parent_id'] !== 0) {
                    $comment['parent_name'] = Comment::where('id', $comment['parent_id'])->first()->user()->first()->name;
                }
                $new_comments[$comment['id']] = $comment;
                $comment_child = $this->index($id, $comment['id']);

                if (! empty($comment_child)) {
                    $new_comments[$comment['id']]['children'] = array_values($comment_child);
                }
            }
        }

        return $new_comments;*/
        $comments = Comment::where('commentable_id', $id)->where('parent_id', 0)->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar', 'created_at');
        }])->get();
        /*$comments = collect($comments)->map(function ($comment) {
            if ($comment['parent_id'] !== 0) {
                $parent_comment = Comment::where('id', $comment['parent_id'])->first()->user()->first();
                return array_merge(
                    $comment->toArray(), [
                      'parent_name' => $parent_comment->name,
                      'parent_user_id' => $parent_comment->id
                    ]
                );
            }
            return $comment->toArray();
        });*/
        return $this->responseSuccess('OK', $comments);
    }

    public function childComments($id)
    {
        $comments = Comment::where('commentable_id', $id)->where('parent_id', Request('parent_id'))
            ->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar', 'created_at');
        }])->get();
        $comments = collect($comments)->map(function ($comment) {
            $parent_comment = Comment::where('id', $comment['parent_id'])->first()->user()->first();
            return array_merge(
                $comment->toArray(), [
                    'parent_name' => $parent_comment->name,
                    'parent_user_id' => $parent_comment->id
                ]
            );
        });
        return $this->responseSuccess('OK', $comments);
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
            return $this->responseSuccess('OK', $comment);
        }
    }
}
