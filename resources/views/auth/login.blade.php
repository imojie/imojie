@extends('layout.default')

@section('title')登录
@stop

@section('content')
    <div id="login-page" class="container">
        <div id="login_btns">
            <a class="btn btn-danger" href="{{route('oauth', ['provider' => 'weibo'])}}">微博登录</a>
            <a class="btn btn-primary" href="{{route('oauth', ['provider' => 'qq'])}}">QQ 登录</a>
        </div>
    </div>
@stop