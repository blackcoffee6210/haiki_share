<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResetPasswordController extends Controller
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

    /**
     * Where to redirect users after resetting their password.
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
        $this->middleware('guest');
    }

	public function showResetForm(Request $request, $token)
	{
		$reset = DB::table('password_resets')->where('token', $token)->first();

		if (!$reset || Carbon::parse($reset->created_at)->addMinutes(1)->isPast()) {
			// パスワードリセットトークンが見つからないか、有効期限が切れている場合
			return response()->json(['error' => 'パスワードリセットのリンクが有効期限切れです。再度リセットリクエストを送信してください。'], 422);
		}

		//有効期限が切れていない場合は、パスワードリセットフォームを表示する
		return view('auth.passwords.reset')->with(
			['token' => $token, 'email' => $request->email]
		);
	}
}
