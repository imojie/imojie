<?php

namespace Imojie\Http\Controllers\Auth;

use Imojie\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    protected $subject = '重置密码';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function getEmail()
    {
        return view('auth.forgot');
    }


    protected function resetPassword($user, $password)
    {
        $user = Sentinel::findById($user->id);
        $user = Sentinel::update($user, ['password' => $password]);
        Sentinel::login($user);
    }
}
