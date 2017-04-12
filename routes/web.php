<?php

Route::post('/deploy', 'DeployController@deploy');
Route::get('/email/verify/{confirm_code}', ['as' => 'email.verify', 'uses' => 'Api\UserController@verifyToken']);