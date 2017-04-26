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
/*
|--------------------------------------------------------------------------
| frontend
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => ['cors', 'api'],
    'prefix' => 'v1',
    'namespace' => 'Api',
], function() {
    //Route::post('user/login', 'UserController@login'); //登录认证
    Route::post('user/register', 'UserController@register'); //注册
    Route::resource('articles', 'QuestionsController'); //话题
    Route::get('topics/all', 'TopicsController@show'); //获取分类标签
    Route::get('hot_articles', 'QuestionsController@hotArticles'); //热门话题
    Route::post('user/get_code', 'UserController@getRegisterCode'); //获取注册码
});

Route::group([
    'middleware' => ['cors', 'api', 'jwt.auth'],
    'prefix' => 'v1',
    'namespace' => 'Api',
], function() {
    Route::any('user/logout', 'UserController@logout'); //退出
    Route::post('article_image', 'QuestionsController@changeArticleImage'); //上传话题图片
});

/*
|--------------------------------------------------------------------------
| backend
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => ['cors', 'api', 'jwt.auth', 'check.permission'],
    'prefix' => 'v1/backend',
    'namespace' => 'Backend',
], function() {
    Route::get('menu', 'MenusController@GetSidebarTree');
    Route::resource('roles', 'RolesController');
    Route::resource('users', 'UsersController');
    Route::resource('permissions', 'PermissionsController');
});

Route::group([
    'middleware' => ['cors', 'api', 'check.permission'],
    'prefix' => 'v1/backend',
    'namespace' => 'Backend',
], function() {
    Route::post('login', 'LoginController@login');
});