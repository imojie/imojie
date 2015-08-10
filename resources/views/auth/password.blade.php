<form method="POST" action="{{action('Auth\PasswordController@postEmail')}}">
    {!! csrf_field() !!}

    <div>
        Email
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div>
        <button type="submit">
            傳送重置密碼連結
        </button>
    </div>
</form>