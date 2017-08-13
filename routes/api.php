<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
    'middleware' => 'cors',
    'prefix' => 'v1',
], function() {
    //Auth
    Route::post('user/login', 'AuthController@login'); //登录认证
    Route::post('user/register', 'AuthController@register'); //注册
    Route::get('/verify_email', 'AuthController@verifyToken'); //验证注册码
    Route::get('user/logout', 'AuthController@logout'); //退出

    //文章分类
    Route::resource('articles', 'ArticlesController'); //文章
    Route::get('hot_articles', 'ArticlesController@hotArticles'); //获取热门话题
    Route::resource('tags', 'TagsController');
    Route::get('hot_tags', 'TagsController@hotTags'); //获取分类标签
    Route::get('article/is_like','LikesController@isLike');//用户是否点赞了一个话题
    Route::get('article/like','LikesController@likeThisArticle');//用户点赞一个话题
});