<?php

Route::post('/deploy', 'DeployController@deploy'); //代码自动部署
Route::get('/email/verify/{confirm_code}', [
    'as' => 'email.verify',
    'uses' => 'Api\UserController@verifyToken'
]); //邮箱验证