<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
		//var_dump(url()->previous());
		\Session::put('url.intended', url()->previous() );
		$this->middleware('guest')->except('logout');
		
	}
	// 踢出前一個user
	protected function authenticated($request, $user)
    {


		//$last_session = ($user->last_session_id != null) ? \Session::getHandler()->read($user->last_session_id) : null;
		//if ($last_session) {
		\Session::getHandler()->destroy($user->last_session_id);

			
		//}
		$user->last_session_id = \Session::getId();
		$user->save();
		//$url = \Session::get('url.intended', url('/'));
		$url = \Session::get('url.intended');
		if (!$url) $url = url('/');

		return response()->json(['url' => $url]);
		//return redirect($url);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
	 * override it
     */
    public function login(Request $request)
    {
		$request->merge(['remember' => '']);

		$this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

	protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string',
			'captcha' => 'required|captcha',
        ],[
			'captcha.captcha' => '验证码错误',
			'captcha.required' => '请输入验证码',
		]);
    }


    public function username()
    {
        return 'name';
    }

	public function showLoginForm()
	{
		return view('auth.login');
	}



    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
		if ($request->response == 'json') {
			return response()->json(['url' => url('/')]);	
		}

        return redirect('/');
    }


	public function refereshcapcha()
	{
		 return captcha_img('flat');
	}
}
