<?php

namespace Imojie\Http\Controllers\Auth;

use Illuminate\Support\Facades\Session;
use Imojie\User;
use Validator;
use Imojie\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectPath = '/';

    const OAUTH_USER = 'oauth_user';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }


    public function weibo()
    {
        // http://socialiteproviders.github.io/providers/weibo/
        return \Socialite::with('weibo')->scopes(array('all', 'email'))->redirect();
    }

    public function qq()
    {
        // http://socialiteproviders.github.io/providers/qq/
        return \Socialite::with('qq')->redirect();
    }


    public function github()
    {
        return \Socialite::with('github')->redirect();
    }


    public function getBind()
    {
        if (!Session::has(self::OAUTH_USER)) {
            return redirect($this->loginPath());
        }
        $oauthUser = Session::get(self::OAUTH_USER);

        // 已经绑定了账号，直接登录
        $localUser = User::where('weibo', $oauthUser->getUid)->first();
        if ($localUser) {
            \Auth::login($localUser);
            return redirect($this->redirectPath());
        }

        return view('auth.bind');
    }


    public function postBind(Request $request)
    {
        if (!Session::has(self::OAUTH_USER)) {
            return redirect($this->loginPath());
        }
        $oauthUser = Session::get(self::OAUTH_USER);

        // 已经绑定了账号，直接登录
        $localUser = User::where('weibo', $oauthUser->getUid)->first();
        if ($localUser) {
            \Auth::login($localUser);
            return redirect($this->redirectPath());
        }

        $data = array_merge(Session::get(self::OAUTH_USER), $request->all());

        $validator = $this->validator($data);

        if ($validator->fails()) {
            $this->throwValidationException(
                $data, $validator
            );
        }

        \Auth::login($this->create($data));

        return redirect($this->redirectPath());
    }


    public function callback()
    {
        $oauthUser = \Socialite::with('weibo')->user();

        // 已经绑定了账号，直接登录
        $localUser = User::where('weibo', $oauthUser->getId())->first();
        if ($localUser) {
            \Auth::login($localUser);
            return redirect($this->redirectPath());
        }

        // 跳转到绑定账号的页面
        \Session::put(self::OAUTH_USER, $oauthUser);
        return redirect(action('Auth\AuthController@bind'));
    }


}
