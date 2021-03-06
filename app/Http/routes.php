<?php

// 首页
Route::get('/', function () {
    return view('page.index');
});

// 注册
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get('auth/registered', 'Auth\AuthController@getRegistered');
Route::get('auth/activation', 'Auth\AuthController@getActivation');
Route::post('auth/activation', 'Auth\AuthController@postActivation');

// 登录
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::post('auth/logout', 'Auth\AuthController@postLogout');

// 第三方登录
Route::get('oauth/{provider}', array(
    'as' => 'oauth', 'uses' => 'Auth\AuthController@oauth'
))->where('provider', 'qq|weibo');
Route::get('oauth/{provider}/callback', array(
    'uses' => 'Auth\AuthController@callback'
))->where('provider', 'qq|weibo');

// 第三方登录账号绑定
Route::get('auth/bind', 'Auth\AuthController@getBind');
Route::post('auth/bind', 'Auth\AuthController@postBind');

// 发送密码重置邮件
Route::get('auth/forgot', 'Auth\PasswordController@getEmail');
Route::post('auth/forgot', 'Auth\PasswordController@postEmail');

// 重置密码
Route::get('auth/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('auth/reset', 'Auth\PasswordController@postReset');


// 用户主页
Route::get('/home', 'UserController@home');
Route::get('/u/{username}', 'UserController@index');

// 用户设置
Route::get('user/settings/info', 'UserController@edit');
Route::post('user/settings/info', 'UserController@update');
Route::get('user/settings/email', 'UserController@getEmail');
Route::post('user/settings/email', 'UserController@postEmail');
Route::get('user/settings/password', 'UserController@getPassword');
Route::post('user/settings/password', 'UserController@postPassword');
Route::get('user/settings/oauth', 'UserController@getOauth');
Route::post('user/settings/oauth', 'UserController@postOauth');
