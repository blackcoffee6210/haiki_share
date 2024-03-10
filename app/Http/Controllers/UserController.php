<?php

namespace App\Http\Controllers;

use App\History;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\UpdateUser;
use App\Mail\OldEmailNotification;
use App\Mail\UpdateEmailNotification;
use App\Product;
use App\Review;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
	    $this->middleware('auth')
		     ->except(['index', 'posted', 'wasReviewed']); //認証なしでアクセスしたいAPIはexceptに書く
    }

	public function index(string $id) { //ユーザー情報取得
    	$user = User::find($id);
    	return response($user, 200);
	}

	public function shopUser(string $id) //購入した商品のコンビニユーザーを取得
	{
		$shopUser = DB::table('products')
					  ->join('users', 'products.user_id', '=', 'users.id')
					  ->where('products.id', '=', $id)
					  ->where('users.group', '=', 2)
					  ->get();
		return $shopUser;
	}

	public function updateProfile(UpdateUser $request) //プロフィール更新
	{
		$user = User::find($request->id); //DBからユーザー情報を取得する

		if($request->file('image')) { //画像が送信されていたらバリデーションを行う
			$request->validate([ 'image' => 'file|mimes:jpg,jpeg,png' ]);

			$original_name = $request->file('image')->getClientOriginalName();                        //formDataから画像の名前を取得する
			$file_name     = date('Ymd_His') . '_' . $original_name;                                //保存する画像名を作成
			$image_path    = $request->file('image')->storeAs('public/images', $file_name);      //名前を変更して保存。保存先のパスを返す
			$image_path    = str_replace('public/images/', '/storage/images/', $image_path); //HTML出力用の整形とパスの修正

		}else { //画像が送信されていなかったらDBの画像を使う
			$image_path = $user->image;
		}

		$old_email = $user->getOriginal('email'); //変更前のEmailアドレスを変数に入れる

		$user->image     = $image_path;
		$user->name      = $request->name;
		$user->branch    = $request->branch;
		$user->address   = $request->address;
		$user->email     = $request->email;
		$user->introduce = $request->introduce ?? '';
		$user->save();

		//===========================================================
		//todo: メールアドレスが変更されたら、更新元と更新先にメールを送る
		if($old_email !== $request->email) { //DBのEメールアドレスとrequestのEメールが違ったら（変更されていたら）確認メールを送る

			$params = [ //メール送信に必要な情報を用意
				'user_id'   => Auth::id(),
				'name'      => $user->name,
				'old_email' => $old_email,
				'new_email' => $user->email
			];

			Mail::to($old_email)->send(new UpdateEmailNotification($params));   //変更前のメールアドレスに送信
			Mail::to($user->email)->send(new UpdateEmailNotification($params)); //変更後のメールアドレスに送信
		}
		//===========================================================

		return response($user, 200);
	}

	public function updatePassword(UpdatePassword $request) //パスワード変更
	{
		$user = Auth::user(); //ログインユーザーの情報を変数に入れる
		$user->password = bcrypt($request->get('new_password')); //新しいパスワードをセットする
		$user->save();

		return response($user, 200);
	}

	public function posted(string $id) //出品した商品取得
	{
		return User::find($id)
				   ->products()
				   ->orderByDesc('products.updated_at')
				   ->get();
	}

	public function purchased(string $id) //購入した商品一覧(利用者)
	{
		$products = Product::whereHas('history', function($q) {
			$q->where('buyer_id', Auth::id());
		})->get();
		return $products;
	}

	public function wasPurchased(string $id) //購入された商品一覧(コンビニ)
	{
		$products = Product::whereHas('history', function($q) {
						$q->where('seller_id', Auth::id());
					})->get();
		return $products;
	}

	public function liked(string $id) //いいねした商品一覧(利用者)
	{
		return User::find($id)
				   ->likes()
				   ->orderByDesc('likes.created_at')
				   ->get();
	}

	public function canceled() //キャンセルした商品一覧(利用者)
	{
		$products = Product::whereHas('cancels', function($q) {
						$q->where('cancel_user_id', Auth::id());
					})->get();
		return $products;
	}

	public function wasCanceled() //キャンセルされた商品一覧（コンビニ）
	{
		$products = Product::whereHas('cancels', function($q) {
			$q->where('post_user_id', Auth::id());
		})->get();
		return $products;
	}

	public function deleted() //削除した商品一覧（コンビニ）
	{
		$products = Product::where('user_id', Auth::id())
						   ->onlyTrashed()
						   ->orderByDesc('deleted_at')
						   ->get();
		return $products;
	}

	public function reviewed(string $id) //レビュー一覧(利用者)
	{
		return User::find(Auth::id())
			->senderReviews()
			->orderByDesc('reviews.created_at')
			->get();
	}

	public function wasReviewed(string $id) //レビュー一覧(コンビニ)
	{
		return Review::where('receiver_id', $id)
					 ->orderByDesc('reviews.created_at')
					 ->get();

	}

	public function destroy(string $id) //ユーザー削除
	{
		User::find($id)->delete(); //ユーザー情報を取得して論理削除する
		return response(['user_id' => $id], 200);
	}
}
