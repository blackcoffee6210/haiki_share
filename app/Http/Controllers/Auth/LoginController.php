<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request, $user) //authenticatedメソッドをオーバーライドしてレスポンスをカスタマイズする
    {
    	return $user;
    }

	protected function validateLogin(Request $request) //validateLoginメソッドをオーバーライド（ログイン画面からのリクエストをカスタマイズ）
	{
		$request->validate([
			$this->username() => 'required|string',
			'password'        => 'required|string',
			'group'           => 'required|integer'
		]);
	}

	protected function credentials(Request $request) //credentialsメソッドをオーバーライド（loginFormのgroupプロパティを取得するため）
	{
		return $request->only($this->username(), 'password', 'group');
	}

    protected function loggedOut(Request $request) //loggedOutメソッドをオーバーライドしてレスポンスをカスタマイズする
    {
	    $request->session()->regenerate(); //セッションを再生成する
	    return response()->json();
    }
}
