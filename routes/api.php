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
    Route::post('user/get_code', 'AuthController@getRegisterCode'); //获取注册码
    Route::get('user/logout', 'AuthController@logout'); //退出

    //文章分类
    Route::resource('articles', 'ArticlesController'); //文章
    Route::get('topics', 'TopicsController@index'); //获取分类标签
});