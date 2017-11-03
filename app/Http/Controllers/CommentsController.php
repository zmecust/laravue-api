<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use App\Notifications\CommentArticleNotification;
use Auth;
use Carbon\Carbon;

class CommentsController extends Controller
{
    /**
     * CommentsController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', [
            'only' => ['store']
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($id)
    {
        $comments = Comment::where('commentable_id', $id)->where('parent_id', 0)->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar');
        }])->get();
        return $this->responseSuccess('OK', $comments);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function childComments($id)
    {
        $parent_id = Request('parent_id');
        $new_comments = [];
        $this->getChildComments($id, $parent_id, $new_comments);
        return $this->responseSuccess('查询成功', $new_comments);
    }

    /**
     * @param $id
     * @param $parent_id
     * @param $new_comments
     */
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
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
            $article = $article->first();
            $data = [
                'name' => $user->name,
                'user_id' => $user->id,
                'title' => $article->title,
                'title_id' => $article->id,
                'comment' => $comment->body
            ];
            if ($comment->parent_id !== 0) {
                $parent = Comment::where('id', $comment->parent_id)->first()->user()->first();
                $comment->parent_name = $parent->name;
                $comment->parent_user_id = $parent->id;
                $parent->notify(new CommentArticleNotification($data));
            } else {
                $article->user->notify(new CommentArticleNotification($data));
            }

            return $this->responseSuccess('OK', $comment);
        }
        return $this->responseError('Has Something Wrong');
    }
}
