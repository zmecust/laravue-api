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
    Route::get('github','AuthController@github'); //第三方账号登录
    Route::post('user/register', 'AuthController@register'); //注册
    Route::get('verify_email', 'AuthController@verifyToken'); //验证注册码
    Route::get('user/logout', 'AuthController@logout'); //退出

    //文章分类
    Route::resource('articles', 'ArticlesController'); //所有文章
    Route::post('content_image', 'ArticlesController@contentImage'); //上传文章图片
    Route::get('hot_articles', 'ArticlesController@hotArticles'); //获取热门话题
    Route::resource('tags', 'TagsController'); //标签
    Route::get('hot_tags', 'TagsController@hotTags'); //获取分类标签
    Route::get('articles/{article}/comments', 'CommentsController@index'); //获取文章的评论
    Route::get('articles/{article}/child_comments', 'CommentsController@childComments'); //获取文章的子评论
    Route::post('comments', 'CommentsController@store'); //增加文章的评论
    Route::get('categories', 'CategoriesController@index'); //获取文章的分类

    //用户相关
    Route::resource('users', 'UsersController');
    Route::post('edit_password', 'UsersController@editPassword'); //修改密码
    Route::get('users/{user}/articles', 'UsersController@userArticles'); //用户发表的文章
    Route::get('users/{user}/replies', 'UsersController@userReplies'); //用户的回复
    Route::get('article/is_like','LikesController@isLike');//用户是否点赞了一个话题
    Route::get('article/like','LikesController@likeThisArticle');//用户点赞一个话题
    Route::get('user/is_follow','FollowsController@isFollow');//用户是否关注一个用户
    Route::get('user/follow','FollowsController@followThisUser');//用户关注一个用户

});

/*
|--------------------------------------------------------------------------
| 后台管理的 API 接口
|--------------------------------------------------------------------------
*/
Route::group([
    'middleware' => 'cors',
    'namespace' => 'Admin',
    'prefix' => 'v1/admin',
], function() {
    Route::post('login', 'AuthController@login'); //后台登录
});

Route::group([
    'middleware' => ['cors', 'jwt.auth', 'check.permission'],
    'namespace' => 'Admin',
    'prefix' => 'v1/admin',
], function() {
    Route::get('menu', 'MenusController@getSidebarTree')->name('users.menu'); //获取后台左侧菜单
    Route::get('group_permissions', 'PermissionsController@groupPermissions'); //获取权限组
    Route::resource('roles', 'RolesController');
    Route::resource('users', 'UsersController');
    Route::resource('menus', 'MenusController');
    Route::resource('permissions', 'PermissionsController');
    Route::get('logout', 'LoginController@logout');
});