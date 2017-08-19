<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;

class CommentsController extends Controller
{
    public function index($id, $parent_id = 0)
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
        $comments = Comment::where('commentable_id', $id)->with(['user' => function ($query) {
            $query->select('id', 'name', 'avatar', 'created_at');
        }])->get();
        $comments = collect($comments)->map(function ($comment) {
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
        });
        return $this->responseSuccess('OK', $comments);
    }
}
