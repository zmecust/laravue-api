<?php

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
/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group([
    'middleware' => ['Cors','api'],
    'prefix' => 'v1',
    'namespace' => 'Api',
], function() {
    Route::post('user/login', 'UserController@login'); //登录认证
    Route::post('user/register', 'UserController@register'); //注册
    Route::resource('articles', 'QuestionsController'); //话题
    Route::get('topics/all', 'TopicsController@show');
});

Route::group([
    'middleware' => ['Cors','jwt.auth'],
    'prefix' => 'v1',
    'namespace' => 'Api',
], function() {
    Route::any('user/logout', 'UserController@logout'); //退出
    Route::post('article_image', 'Questions@changeArticleImage');
});