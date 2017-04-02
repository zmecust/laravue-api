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
    'middleware' => ['cors','api'],
    'prefix' => 'v1',
    'namespace' => 'Api',
], function() {
    Route::post('user/login', 'UserController@login'); //登录认证
    Route::post('user/register', 'UserController@register'); //注册
});