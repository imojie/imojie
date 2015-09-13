<?php

namespace Imojie\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Laravel\Socialite\Facades\Socialite;
use Imojie\Http\Controllers\Controller;
use Imojie\Http\Requests\LoginRequest;
use Imojie\Http\Requests\BindAccountRequest;
use Imojie\Models\Auth\ThrottlesLogins;
use Imojie\Models\Auth\AuthenticatesAndRegistersUsers;
use Imojie\Models\User;
use Imojie\Models\OAuthAccount;


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
        $this->middleware('guest', ['except' => 'postLogout']);
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


    public function getRegister()
    {
        return view('auth.register');
    }


    public function postRegister(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|unique:users',
        ], [
            'email.unique' => '该邮箱已经被注册激活',
        ]);

        $email = $request->input('email');

        // 注册用户，但是先不激活
        $credentials = [
            'email' => $email,
            'password' => md5(time()),
        ];
        $user = Sentinel::register($credentials, false);

        // 生成激活码
        $activation = Activation::create($user);

        // 发邮件
        $vars = [
            'email' => $email,
            'code' => $activation->code,
        ];
        Mail::queue('emails.register', $vars, function ($message) use ($email) {
            $message->to('877131516@qq.com', $email)->subject('感谢您的注册');
        });

        return redirect('auth/registered');
    }


    public function  getRegistered()
    {
        return view('auth.registered');
    }


    public function getActivation()
    {
        return view('auth.activation');
    }


    public function postActivation()
    {
        $user = Sentinel::findByCredentials(array(
            'email' => 'u9@imojie.com',
        ));

        \DB::connection()->enableQueryLog();
        $r = Sentinel::update($user, array(
            'last_name' => 'xxxxxx',
            'password' => '111111',
        ));
//        var_dump($r);
        var_dump(\DB::getQueryLog());

//        激活用户账号
        $s = Activation::complete($user, '5gO3zG3FQguFqvqgAmfoiWmiK5nijZHi');

        return redirect($this->redirectPath());
    }


    public function getLogin()
    {
        return view('auth.login');
    }


    public function postLogin(LoginRequest $request)
    {
        // 验证账号
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        $remember = $request->has('remember') ? true : false;
        $user = Sentinel::authenticate($credentials, $remember);
        if (!$user) {
            return redirect()->back()->withInput($request->except(array('password')))
                ->withErrors(array('账号或密码错误'));
        }
        return redirect($this->redirectPath());
    }


    public function postLogout()
    {
        Sentinel::logout();
        return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : '/');
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

        $oauthUser = Socialite::with($provider)->user();

        $uid = OAuthAccount::where('oauth_id', $oauthUser->getId())
            ->where('oauth_type', $provider)->pluck('uid');

        if ($uid && $user = Sentinel::findById($uid)) {
            Sentinel::login($user);
            return redirect($this->redirectPath());
        }

        // 如果当前第三方账号没有绑定我站账号，那么跳转到绑定账号的页面
        Session::put(self::OAUTH_USER, array(
            'provider' => $provider,
            'user' => $oauthUser,
        ));
        return redirect()->action('Auth\AuthController@getBind');
    }


    public function getBind()
    {
        if (!Session::has(self::OAUTH_USER)) {
            return redirect($this->loginPath());
        }
        $oauthInfo = Session::get(self::OAUTH_USER);
        $provider = $oauthInfo['provider'];
        $oauthUser = $oauthInfo['user'];

        // 已经绑定了账号，直接登录
        $uid = OAuthAccount::where('oauth_id', $oauthUser->getId())
            ->where('oauth_type', $provider)->pluck('uid');
        if ($uid && $user = Sentinel::findById($uid)) {
            Sentinel::login($user);
            return redirect($this->redirectPath());
        }

        return view('auth.bind');
    }


    public function postBind(BindAccountRequest $request)
    {
        if (!Session::has(self::OAUTH_USER)) {
            return redirect($this->loginPath());
        }
        $oauthInfo = Session::get(self::OAUTH_USER);
        $provider = $oauthInfo['provider'];
        $oauthUser = $oauthInfo['user'];

        // 已经绑定了账号，直接登录
        $uid = OAuthAccount::where('oauth_id', $oauthUser->getId())
            ->where('oauth_type', $provider)->pluck('uid');
        if ($uid && $user = Sentinel::findById($uid)) {
            Sentinel::login($user);
            return redirect($this->redirectPath());
        }

        // 验证账号
        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ];
        $user = Sentinel::authenticate($credentials, false);
        if (!$user) {
            return redirect()->back()->withInput($request->except(array('password')))
                ->withErrors(array('账号或密码错误'));
        }

        // 绑定账号
        $oAuthAccount = new OAuthAccount();
        $oAuthAccount->uid = $user->id;
        $oAuthAccount->oauth_id = $oauthUser->getId();
        $oAuthAccount->oauth_type = $provider;
        $oAuthAccount->created_at = time();
        $oAuthAccount->save();

        Session::forget(self::OAUTH_USER);

        return redirect($this->redirectPath());
    }


}
