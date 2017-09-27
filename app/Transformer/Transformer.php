<?php
namespace App\Transformer;
/**
 * Created by PhpStorm.
 * User: zm
 * Date: 2017/2/28
 * Time: 12:29
 */
abstract class Transformer
{
    public function transformCollection($items){
        return array_map([$this, 'transform'], $items);
    }

    public abstract function transform($item);
}