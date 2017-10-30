<?php
/**
 * Created by PhpStorm.
 * User: zhangmin
 * Date: 2017/10/30
 * Time: 17:28
 */
namespace App\Transformer;

class ArticleLikesTransformer extends Transformer
{
    public function transform($item)
    {
        return [
            'id' => $item['id'],
            'name' => $item['name'],
            'avatar' => $item['avatar']
        ];
    }
}