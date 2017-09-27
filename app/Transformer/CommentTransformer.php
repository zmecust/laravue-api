<?php
/**
 * Created by PhpStorm.
 * User: zhangmin
 * Date: 2017/9/27
 * Time: 17:59
 */

namespace App\Transformer;


class CommentTransformer extends Transformer
{
    public function transform($item)
    {
        return [
            'body' => $item['body'],
            'created_at' => $item['created_at'],
            'commentable' => [
                'id' => $item['commentable']['id'],
                'title' => $item['commentable']['title'],
                'comments_count' => $item['commentable']['comments_count'],
                'likes_count' => $item['commentable']['comments_count'],
            ]
        ];
    }
}