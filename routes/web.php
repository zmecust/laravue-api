<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//代码自动部署
Route::post('deploy', 'DeployController@deploy');

//第三方账号回调登录
Route::get('github','GithubLoginController@github');
Route::get('github/login','GithubLoginController@githubLogin');
Route::get('github/login/redirect','GithubLoginController@getUserInfo')->middleware('cors');

//生成文档
//Route::get('/swagger', function(){
//    $swagger = \Swagger\scan(public_path('/../doc/ApiDoc.php'));
//    header('Content-Type: application/json');
//    return $swagger;
//});
