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
        $user = User::where('first_name', $username)->first();
        var_dump(\DB::getQueryLog());
        var_dump($user);
        return $user->first_name;
    }


    public function home()
    {
        $user = Sentinel::getUser();
        return view('user.home', compact('user'));
    }


    public function edit()
    {
        $user = Sentinel::getUser();
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
        return view('user.password');
    }


    public function postPassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'new_password' => 'required|confirmed',
        ], [
            'current_password.required' => '当前密码不能为空',
            'new_password.required' => '新密码不能为空',
            'new_password.confirmed' => '新密码和重复新密码必须一致',
        ]);

        // 验证当前密码
        $credentials = [
            'email' => Sentinel::getUser()->email,
            'password' => $request->get('current_password'),
        ];

        $user = Sentinel::authenticate($credentials);
        if (!$user) {
            return redirect()->back()->withErrors(['current_password' => '当前密码错误']);
        }

        // 修改新密码
        Sentinel::update($user, ['password' => $request->get('new_password')]);

        return redirect()->back()->with('message', '修改成功');
    }


    public function getEmail()
    {
        $user = Sentinel::getUser();
        return view('user.email', compact('user'));
    }

    public function postEmail()
    {

    }

    public function getOauth()
    {
        return view('user.oauth');
    }

    public function postOauth()
    {

    }
}
