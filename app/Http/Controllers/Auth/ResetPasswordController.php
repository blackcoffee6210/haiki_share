<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
	use ResetsPasswords;

	public function __construct()
	{
		$this->middleware('guest');
	}

	public function find($token) // トークンの有効性を確認するメソッド
	{
		$email = request()->query('email', '');
		Log::info("検索されたメールアドレス: {$email}");

		$user = User::where('email', $email)->first();

		if (!$user) {
			Log::info("ユーザーが見つかりません: {$email}");
			return response()->json(['error' => 'ユーザーが見つかりません。'], 404);
		}

		if(Password::broker()->tokenExists($user, $token)) {
			Log::info("トークンは有効です: {$token}");
			return response()->json(['message' => 'トークンは有効です。']);
		} else {
			Log::info("トークンは無効、または期限が切れています: {$token}");
			return response()->json(['error' => 'このトークンは無効、または期限が切れています。'], 401);
		}
	}

	public function reset(Request $request)
	{
		$request->validate([
			'token'    => 'required',
			'email'    => 'required|email',
			'password' => 'required|max:255|confirmed|min:8',
		]);

		$user = User::where('email', $request->email)->first(); //まずユーザーを特定します

		if (!$user) { //ユーザーが見つからない場合はエラー
			return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
		}

		if (Hash::check($request->password, $user->password)) { //以前のパスワードと同じかチェック
			return response()->json(['errors' => ['password' => ['新しいパスワードは以前のパスワードと異なるものを入力してください。']]], 422);
		}

//		if (Hash::check($request->password, $user->password)) { //以前のパスワードと同じかチェック
//			return response()->json(['errors' => '新しいパスワードは以前のパスワードと異なるものを入力してください。'], 422);
//		}

		try {
			$status = Password::reset(
				$request->only('email', 'password', 'password_confirmation', 'token'),
				function ($user, $password) {
					$user->password = Hash::make($password);
					$user->save();
					Auth::login($user);
				}
			);
			if ($status == Password::PASSWORD_RESET) {
				return response()->json(['message' => 'パスワードがリセットされました。'], 200);
			} else {
				return response()->json(['error' => 'パスワードをリセットできませんでした。'], 400);
			}

		} catch (\Exception $e) {
			Log::error('パスワードのリセット処理中にエラーが発生しました: ' . $e->getMessage());
			return response()->json(['error' => 'パスワードのリセット処理中にエラーが発生しました。'], 500);
		}
	}
}
