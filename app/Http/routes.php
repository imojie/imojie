<?php

// 首页
Route::get('/', function () {
    return '首页';
});

// 用户主页
Route::get('/home', 'UserController@home');
Route::get('/u/{username}', 'UserController@index');

// 登录
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// 第三方登录
Route::get('auth/github', 'Auth\AuthController@github');
Route::get('auth/weibo', 'Auth\AuthController@weibo');
Route::get('auth/weibo/callback', 'Auth\AuthController@callback');
Route::get('auth/qq', 'Auth\AuthController@qq');

Route::get('oauth/{provider}', array(
    'as' => 'oauth', 'uses' => 'Auth\AuthController@oauth'
));
//->where('provider', 'qq|weibo');
Route::get('oauth/{provider}/callback', array(
    'uses' => 'Auth\AuthController@callback'
));
//->where('provider', 'qq|weibo');

// 第三方登录账号绑定
Route::get('auth/bind/{provider}', 'Auth\AuthController@getBind');
Route::post('auth/bind', 'Auth\AuthController@postBind');

// 注册
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// 发送密码重置邮件
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// 重置密码
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');


Route::get('user/edit', 'UserController@edit');
Route::post('user/update', 'UserController@update');
