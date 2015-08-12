<?php

namespace Imojie\Http\Controllers;


use Imojie\User;

class UserController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }


    public function index($username)
    {
        $user = User::where('name', $username)->first();
        dd(\DB::getQueryLog());
        return $user->name;
    }

    public function home()
    {
        $user = \Auth::user();
        return $user->email;
    }
}
