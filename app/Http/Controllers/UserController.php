<?php

namespace App\Http\Controllers;

use App\EmailUpdate;
use App\Http\Requests\UpdateEmail;
use App\Http\Requests\UpdatePassword;
use App\Http\Requests\UpdateUser;
use App\Mail\OldEmailNotification;
use App\Mail\UpdateEmailNotification;
use App\Product;
use App\Review;
use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function __construct()
    {
	    $this->middleware('auth')
		     ->except(['index', 'posted', 'wasReviewed']); //認証なしでアクセスしたいAPIはexceptに書く
    }

	public function index(string $id) { //ユーザー情報取得
		try {
			$user = User::find($id);
			if (!$user) { //取得できなかったら
				return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
			}
			return response($user, 200);
		} catch (\Exception $e) {
			Log::error('ユーザー情報取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'ユーザー情報の取得に失敗しました。'], 500);
		}
	}

	public function shopUser(string $id) //購入した商品のコンビニユーザーを取得
	{
		try {
			$shopUser = DB::table('products')
				->join('users', 'products.user_id', '=', 'users.id')
				->where('products.id', '=', $id)
				->where('users.group', '=', 2)
				->get();
			return response($shopUser, 200);
		} catch (\Exception $e) {
			Log::error('コンビニユーザー取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'コンビニユーザーの取得に失敗しました。'], 500);
		}
	}

	public function updateProfile(UpdateUser $request) //プロフィール更新
	{
		try {
			$user = User::find($request->id); //DBからユーザー情報を取得する
			if(!$user) { //取得できなかったら
				return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
			}

			if($request->file('image')) { //画像が送信されていたらバリデーションを行う
				$request->validate([ 'image' => 'file|mimes:jpg,jpeg,png' ]);
				$original_name = $request->file('image')
										 ->getClientOriginalName(); //formDataから画像の名前を取得する
				$file_name     = date('Ymd_His'). '_'. $original_name; //保存する画像名を作成
				$image_path    = $request->file('image')
										 ->storeAs('public/images', $file_name); //名前を変更して保存
				$image_path    = str_replace( //HTML出力用の整形とパスの修正
					'public/images/',
					'/storage/images/',
					$image_path);

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

			if($old_email !== $request->email) { //Eメールが変更されていたら確認メールを送る
				$params = [ //メール送信に必要な情報を用意
					'user_id'   => Auth::id(),
					'name'      => $user->name,
					'old_email' => $old_email,
					'new_email' => $user->email
				];

				Mail::to($old_email)->send(new UpdateEmailNotification($params));   //変更前のメールアドレスに送信

				return response($user, 200);
			}

		}catch (\Exception $e) {
			Log::error('プロフィール更新に失敗しました: ' . $e->getMessage()); // エラーをログに記録
			return response()->json(['message' => 'プロフィールの更新に失敗しました。'], 500); //エラーメッセージを返す
		}
	}

	public function updateEmail(UpdateEmail $request, $id) //Eメール変更
	{
		DB::beginTransaction(); //トランザクション開始

		try {
			$user     = User::findOrFail($id);
			$newEmail = $request->email;
			$oldEmail = $user->email;

			if($oldEmail === $newEmail) { //新しいEメールアドレスが現在のものと異なることを確認
				return response()->json(['errors' => ['email' => ['新しいEメールアドレスは現在のアドレスと同じです。']]], 422);
			}

			$token = Str::random(60); //Eメール更新確認用のトークン生成

			EmailUpdate::create([ //DBに保存
				'user_id'   => $user->id,
				'new_email' => $newEmail,
				'token'     => $token,
			]);

			try {
				Mail::to($newEmail)->send(new UpdateEmailNotification($user, $token)); //確認メールを新しいEメールアドレスに送信
			}catch (\Exception $e) {
				Log::error('新しいEメールへの確認メール送信に失敗しました: ', ['error' => $e->getMessage()]);
				return response()->json(['message' => '新しいEメールへの確認メール送信に失敗しました'], 500); //エラーメッセージを返す
			}

			try {
				Mail::to($oldEmail)->send(new OldEmailNotification($user->name)); //更新通知を古いEメールアドレスに送信
			}catch (\Exception $e) {
				Log::error('古いEメールへの通知メール送信に失敗しました: ', ['error' => $e->getMessage()]);
				return response()->json(['message' => '古いEメールへの通知メール送信に失敗しました'], 500); //エラーメッセージを返す
			}

			DB::commit(); //トランザクションをコミットし、変更を確定

			return response()->json(['message' => '確認メールを送信しました。メール内のリンクからEメールの更新を完了してください。']);

		}catch(\Exception $e) {
			DB::rollBack(); //エラーが発生した場合は、トランザクションをロールバック
			Log::error('Eメール変更に失敗しました: ', ['error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
			return response()->json(['message' => 'Eメールの変更に失敗しました。'], 500);
		}
	}

	public function confirmEmail($token) //トークンを使ってEメール更新のリクエストを確認
	{
		DB::beginTransaction();

		try {
			$emailUpdate = EmailUpdate::where('token', $token)
				->where('created_at', '>', now()->subMinutes(1)) //60分
				->firstOrFail();

//			$emailUpdate = EmailUpdate::where('token', $token)
//				->where('created_at', '>', now()->subMinutes(1)) //60分
//				->firstOrFail();

			$user = User::findOrFail($emailUpdate->user_id); //ユーザーが見つからない場合はここで例外が発生
			$user->email = $emailUpdate->new_email;
			$user->save(); //Eメールアドレスを更新

			$emailUpdate->delete(); //トークンを削除
			DB::commit(); //トランザクションをコミットし、変更を確定

			return response()->json(['message' => 'Eメールが更新されました'], 200);

		}catch (ModelNotFoundException $e) {
			DB::rollBack();
			Log::error('このリクエストは期限切れか、無効です: '. $e->getMessage());
			return response()->json(['message' => 'このリクエストは期限切れか、無効です'], 404);

		}catch (\Exception $e) {
			DB::rollBack();
			Log::error('Eメールの更新処理に失敗しました: '. $e->getMessage());
			return response()->json(['message' => 'Eメールの変更に失敗しました。'], 500);
		}
	}

	public function updatePassword(UpdatePassword $request) //パスワード変更
	{
		try {
			$user = Auth::user();
			$user->password = bcrypt($request->get('new_password'));
			$user->save();
			return response($user, 200);
		} catch (\Exception $e) {
			Log::error('パスワード変更に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'パスワードの変更に失敗しました。'], 500);
		}
	}

	public function posted(string $id) //出品した商品取得
	{
		try {
			$user = User::find($id);
			if (!$user) { //取得できなかったら
				return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
			}
			$products = $user->products()->orderByDesc('products.updated_at')->get();
			return $products;
		} catch (\Exception $e) {
			Log::error('出品した商品取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => '出品した商品の取得に失敗しました。'], 500);
		}
	}

	public function purchased(string $id) //購入した商品一覧(利用者)
	{
		try {
			$products = Product::whereHas('history', function($q) {
				$q->where('buyer_id', Auth::id());
			})->get();
			return response($products, 200);
		} catch (\Exception $e) {
			Log::error('購入した商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => '購入した商品の取得に失敗しました。'], 500);
		}
	}

	public function wasPurchased(string $id) //購入された商品一覧(コンビニ)
	{
		try {
			$products = Product::whereHas('history', function($q) {
				$q->where('seller_id', Auth::id());
			})->get();
			return response($products, 200);
		} catch (\Exception $e) {
			Log::error('購入された商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => '購入された商品の取得に失敗しました。'], 500);
		}
	}

	public function liked(string $id) //いいねした商品一覧(利用者)
	{
		try {
			$likes = User::find($id)->likes()->orderByDesc('likes.created_at')->get();
			return response($likes, 200);
		}catch (\Exception $e) {
			Log::error('いいねした商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'いいねした商品の取得に失敗しました。'], 500);
		}
	}

	public function canceled() //キャンセルした商品一覧(利用者)
	{
		try {
			$products =  Product::whereHas('cancels', function($q) {
				$q->where('cancel_user_id', Auth::id());
			})->get();
			return response($products, 200);
		}catch (\Exception $e) {
			Log::error('キャンセルした商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'キャンセルした商品の取得に失敗しました。'], 500);
		}
	}

	public function wasCanceled() //キャンセルされた商品一覧（コンビニ）
	{
		try {
			$products =  Product::whereHas('cancels', function($q) {
				$q->where('post_user_id', Auth::id());
			})->get();
			return response($products, 200);
		}catch (\Exception $e) {
			Log::error('キャンセルされた商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'キャンセルされた商品の取得に失敗しました。'], 500);
		}
	}

	public function deleted() //削除した商品一覧（コンビニ）
	{
		try {
			$products = Product::where('user_id', Auth::id())
				->onlyTrashed()
				->orderByDesc('deleted_at')
				->get();
			return response($products, 200);
		}catch (\Exception $e) {
			Log::error('削除した商品一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => '削除した商品の取得に失敗しました。'], 500);
		}
	}

	public function reviewed(string $id) //レビュー一覧(利用者)
	{
		try {
			$reviews = User::find(Auth::id())
				->senderReviews()
				->orderByDesc('reviews.created_at')
				->get();
			return response($reviews, 200);
		}catch(\Exception $e) {
			Log::error('レビュー一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'レビューの取得に取得に失敗しました。'], 500);
		}
	}

	public function wasReviewed(string $id) //レビュー一覧(コンビニ)
	{
		try {
			$reviews = Review::where('receiver_id', $id)->orderByDesc('reviews.created_at')->get();
			return response($reviews, 200);
		} catch (\Exception $e) {
			Log::error('レビューされた一覧取得に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'レビューされた商品の取得に失敗しました。'], 500);
		}
	}

	public function destroy(string $id) //ユーザー削除
	{
		try {
			$user = User::find($id);
			if (!$user) { //取得できなかったら
				return response()->json(['message' => 'ユーザーが見つかりません。'], 404);
			}
			$user->delete();
			return response()->json(['message' => 'ユーザーを削除しました。'], 200);
		} catch (\Exception $e) {
			Log::error('ユーザー削除に失敗しました: ' . $e->getMessage());
			return response()->json(['message' => 'ユーザーの削除に失敗しました。'], 500);
		}
	}
}
