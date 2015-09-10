<?php

namespace Imojie\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Laravel\Socialite\Facades\Socialite;
use Imojie\Http\Controllers\Controller;
use Imojie\Models\Auth\ThrottlesLogins;
use Imojie\Models\Auth\AuthenticatesAndRegistersUsers;
use Imojie\User;


class AuthController extends Controller
{

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    protected $redirectPath = '/home';

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
        return Sentinel::register([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ], true);
    }


    public function oauth($provider)
    {
        $provider = strtolower($provider);
        switch ($provider) {
            case 'qq':
                return Socialite::with('qq')->redirect();
                break;
            case 'weibo':
                return Socialite::with('weibo')->scopes(array('all'))->redirect();
                break;
            default:
                return redirect('auth/login');
        }
    }


    public function callback($provider)
    {
        $provider = strtolower($provider);
        var_dump($provider);

        $oauthUser = Socialite::with($provider)->user();
        var_dump($oauthUser, $oauthUser->getId(), $oauthUser->getNickname(), $oauthUser->getAvatar());
        exit;

        // 已经绑定了账号，直接登录
        $localUser = User::where('weibo', $oauthUser->getId())->first();
        if ($localUser) {
            Auth::login($localUser);
            return redirect($this->redirectPath());
        }

        // 跳转到绑定账号的页面
        Session::put(self::OAUTH_USER, $oauthUser);
        return redirect(action('Auth\AuthController@bind'));
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
            Auth::login($localUser);
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
            Auth::login($localUser);
            return redirect($this->redirectPath());
        }

        $data = array_merge(Session::get(self::OAUTH_USER), $request->all());

        $validator = $this->validator($data);

        if ($validator->fails()) {
            $this->throwValidationException(
                $data, $validator
            );
        }

        Auth::login($this->create($data));

        return redirect($this->redirectPath());
    }


}
