<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

	public function sendResetLinkEmail(Request $request) //追記(オーバーライド)
	{
		$request->validate(['email' => 'required|email|max:255']); //メールアドレスのバリデーション
		$response = Password::sendResetLink($request->only('email')); //パスワードリセットトークンメールの送信

		if($response == Password::RESET_LINK_SENT) { //メール送信成功
			return response(['message' => 'パスワード再設定メールを送信しました。'], 200);
		}else {
//			return response()->json(['message' => 'メール送信に失敗しました。指定されたメールアドレスは見つかりません。'], 404);
			return response()->json(['errors' => ['email' => ['メール送信に失敗しました。指定されたメールアドレスは見つかりません。']]], 422);
		}
	}
}
