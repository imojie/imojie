@extends('layout.default')

@section('title')注册
@stop

@section('content')
    <div id="login-page" class="container">
        <div class="row" id="passport-wrap">
            <div class="col-md-3">
                <ul class="nav nav-pills nav-stacked">
                    <li role="presentation"><a href="{{url('auth/login')}}">登录</a></li>
                    <li role="presentation" class="active"><a href="{{url('auth/register')}}">注册</a></li>
                    <li role="presentation"><a href="{{url('auth/forgot')}}">找回密码</a></li>
                </ul>
            </div>
            <div class="col-md-9">
                <form method="POST" action="{{action('Auth\AuthController@postRegister')}}">
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <p>{{$error}}</p>
                        </div>
                    @endforeach
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="email">邮箱</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email"
                               value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <span>同意并接受 <a target="_blank" href="">《服务条款》</a></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success btn-block">发送邮件</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop