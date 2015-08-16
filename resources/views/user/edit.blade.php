<?php
var_dump($errors->all());
?>
<form method="POST" action="{{action('UserController@update')}}">
    {!! csrf_field() !!}

    <div>
        用户名
        <input type="text" name="name" value="{{$user->name}}">
    </div>

    <div>
        性别
        <label>
            <input type="radio" name="gender" value="0" {{ 0==$user->gender ? 'checked' : ''}}>保密
        </label>
        <label>
            <input type="radio" name="gender" value="1" {{ 1==$user->gender ? 'checked' : ''}}>男
        </label>
        <label>
            <input type="radio" name="gender" value="2" {{ 2==$user->gender ? 'checked' : ''}}>女
        </label>
    </div>

    <div>
        城市
        <input type="text" name="city" value="{{$user->city}}">
    </div>

    <div>
        <button type="submit">保 存</button>
    </div>
</form>