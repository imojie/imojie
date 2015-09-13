<?php

namespace Imojie\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Imojie\Models\User;

class UserController extends Controller
{


    public function __construct()
    {
        \DB::connection()->enableQueryLog();

        $this->middleware('auth', ['except' => ['index']]);
    }


    public function index($username)
    {
        $user = User::where('name', $username)->first();
        dd(\DB::getQueryLog());
        return $user->name;
    }


    public function home()
    {
        $user = Sentinel::getUser();
        return view('user.home', compact('user'));
    }


    public function edit()
    {
        $user = \Auth::user();
        return view('user.edit', compact('user'));
    }


    public function update(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required',
            'gender' => 'required|in:0,1,2',
            'city' => '',
        ));

        $user = \Auth::user();

        $user->name = $request->get('name');
        $user->gender = $request->get('gender');
        $user->city = $request->get('city');

        $user->save();

        return redirect()->back();
    }


    public function getPassword()
    {

    }

    public function postPassword()
    {

    }


    public function getEmail()
    {

    }

    public function postEmail()
    {

    }

    public function oauth()
    {

    }
}
