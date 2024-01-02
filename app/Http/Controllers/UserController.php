<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUser;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
    	//認証
	    $this->middleware('auth');
    }

    //ユーザー情報取得
	public function index(string $id) {
    	$user = User::find($id);
    	return $user;
	}

	//プロフィール更新
	public function updateProfile(UpdateUser $request)
	{
		//DBからユーザー情報を取得する
		$user = User::find($request->id);

		//画像が送信されていたらバリデーションを行う
		if($request->file('image')) {
			$request->validate([
				'image' => 'file|mimes:jpg,jpeg,png'
			]);

			//formDataから画像の名前を取得する
			$original_name = $request->file('image')->getClientOriginalName();
			//保存する画像名を作成
			$file_name = date('Ymd_His') . '_' . $original_name;
			//名前を変更して保存。保存先のパスを返す
			$image_path = $request->file('image')->storeAs('public/images', $file_name);
			//HTML出力用の整形とパスの修正
			$image_path = str_replace('public/images/', '/storage/images/', $image_path);
		}
		//画像が送信されていなかったらDBの画像を使う
		else {
			$image_path = $user->image;
		}

		$user->image     = $image_path;
		$user->name      = $request->name;
		$user->branch    = $request->branch;
		$user->address   = $request->address;
		$user->email     = $request->email;
		$user->introduce = $request->introduce;
		$user->save();

		return response($user, 200);
	}
}
