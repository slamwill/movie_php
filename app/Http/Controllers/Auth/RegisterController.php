<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('pageview');
		$this->redirectTo = '/';
        $this->middleware('guest');
	}
 public function getRegister()
    {
        return view('auth.m-login');
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|unique:users|min:6|string|max:255|regex:/(^([a-zA-Z0-9]+)$)/u',
            //'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
			'captcha' => 'required|captcha',

		],[

			'name.unique' => '帐号已被注册',
			'name.min' => '帐号最少 :min 個字元',
			'name.regex' => '请输入英文字母及数字组合',
			//'email.email' => '必需是信箱格式',
			'password.min' => '密码最少6個字元',
			'password.confirmed' => '密碼與確認密碼不相同',
			'captcha.captcha' => '验证码错误',
			'captcha.required' => '请输入验证码',
		]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return User::create([
            //'name' => $data['name'],
            'name' => $data['name'],
            //'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'source_ip' => $ip,
        ]);
    }
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);
		return response()->json(['status' => 1]);
        /*
		return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());*/
    }

	public function showRegistrationForm(Request $request){
		return view('auth.register');
	}

}
