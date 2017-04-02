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

/*Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index');

Route::get('/email/verify/{confirm_code}', ['as' => 'email.verify', 'uses' => 'UsersController@verifyToken']);

Route::resource('questions', 'QuestionsController');*/

Route::post('/deploy', 'DeployController@deploy');
Route::get('/email/verify/{confirm_code}', ['as' => 'email.verify', 'uses' => 'Api\UsersController@verifyToken']);