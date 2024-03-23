<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReview;
use App\Http\Requests\UpdateReview;
use App\Mail\ReviewedReceiverNotification;
use App\Mail\reviewedSenderNotification;
use App\Product;
use App\Recommendation;
use App\Review;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReviewController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth')
			 ->except(['show', 'otherProducts', 'topPageReview']); //認証なしでアクセスしたいAPIはexceptに書く
	}

	public function show(string $s_id, string $r_id) //レビュー取得
	{
		try {
			$review = Review::with(['sender', 'receiver', 'recommendation'])
							->where('sender_id', $s_id)
							->where('receiver_id', $r_id)
							->firstOrFail();
			return response($review, 200);

		}catch (\Exception $e) {
			Log::error('レビュー詳細の取得に失敗しました: '. $e->getMessage());
			return response()->json(['error' => 'レビュー詳細の取得に失敗しました'], 500);
		}
	}

	public function store(StoreReview $request) //レビュー投稿
	{
		DB::beginTransaction(); //トランザクション開始

		try { //例外処理
			$recommendation = Recommendation::findOrFail($request->recommendation_id); //ユーザー評価を取得（なければ404を返す）
			//=========================================================================
			//DB登録
			$review = new Review; //インスタンス生成
			$review->sender_id         = $request->sender_id;         //送信者のID
			$review->receiver_id       = $request->receiver_id;       //受信者のID
			$review->recommendation_id = $request->recommendation_id; //ユーザー評価のID
			$review->title             = $request->title;             //レビュータイトル
			$review->detail            = $request->detail;            //レビューの内容
			$review->save();
			//=========================================================================

			$sender_email   = Auth::user()->email; //送信者のEmail
			$receiver_email = User::findOrFail($request->receiver_id)->email; //送信者のEmail。存在しない場合は404エラー

			$params = [ //メール送信に必要な情報を用意
				'sender_id'      => $request->sender_id,         //送信者のID
				'sender_name'    => Auth::user()->name,          //送信者の名前
				'sender_image'   => Auth::user()->image,         //送信者の画像
				'receiver_id'    => $request->receiver_id,       //受信者のID
				'receiver_name'  => $request->shopUser['name'],  //受信者の名前
				'receiver_image' => $request->shopUser['image'], //受信者の画像
				'recommendation' => $recommendation['name'],     //ユーザー評価
				'title'          => $request->title,             //レビューのタイトル
				'detail'         => $request->detail,            //レビューの内容
				'reviewed_at'    => Carbon::now()                //レビューした日時
			];
			Mail::to($sender_email)->send(new ReviewedSenderNotification($params));     //送信者にメールを送信
			Mail::to($receiver_email)->send(new ReviewedReceiverNotification($params)); //受信者にメールを送信

			DB::commit();
			return response($review, 201); //レビューの保存に成功した場合

		}catch(\Exception $e) {
			DB::rollBack(); //エラー発生時はトランザクションをロールバック
			Log::error($e->getMessage()); //エラーログを記録
			return response()->json(['message' => 'レビュー投稿に失敗しました'], 500);
		}
	}

	public function update(UpdateReview $request) //レビュー更新
	{
		DB::beginTransaction(); //トランザクション開始
		try {
			$review = Review::where('sender_id', $request->sender_id) //対象レビューの検索(なければ404を返す)
							->where('receiver_id', $request->receiver_id)
							->firstOrFail();

			$review->fill([
				'recommendation_id' => $request->recommendation_id,
				'title'             => $request->title,
				'detail'            => $request->detail
			])->save();

			DB::commit(); //トランザクション確定

			return response($review, 200); //更新後のレビューをレスポンシブとして返す

		}catch(ModelNotFoundException $e) {
			DB::rollBack(); //トランザクションをロールバック
			return response()->json(['message' => 'レビューが見つかりませんでした。'], 404);

		}catch(\Exception $e) {
			Log::error($e->getMessage());
			return response()->json(['message' => 'レビューの更新に失敗しました。'], 500);
		}
	}

	public function destroy(string $s_id, string $r_id) //レビュー削除
	{
		try {
			$review = Review::where('sender_id', $s_id) //レビューがあるか確認(なければ404を返す)
							->where('receiver_id', $r_id)
							->firstOrFail();
			$review->delete();
			return response($review, 200);

		}catch (ModelNotFoundException $e) {
			return response()->json(['message' => 'レビューが見つかりませんでした。'], 404);

		}catch (\Exception $e) {
			Log::error($e->getMessage());
			return response()->json(['message' => 'レビューの削除に失敗しました'], 500);
		}

	}

	public function otherProducts(string $r_id) //出品した商品を取得
	{
		try {
			$products = Product::where('user_id', $r_id)
							   ->orderByDesc('created_at')
							   ->get();
			return response($products, 200);

		}catch (\Exception $e) {
			Log::error('出品した商品を取得できませんでした: '. $e->getMessage());
			return response()->json(['message' => '出品した商品を取得できませんでした'], 500);
		}
	}

	public function topPageReview() //topページに表示するレビューをランダムで3件取得
	{
		try {
			$reviews = Review::inRandomOrder()->take(3)->get();
			if($reviews->isEmpty()) {
				return response()->json(['message' => 'レビューが見つかりませんでした'], 404);
			}
			return $reviews;

		}catch (\Exception $e) {
			Log::error('topページに表示するレビューを取得中にエラーが発生しました: '. $e->getMessage());
			return response()->json(['message' => 'topページに表示するレビューを取得中にエラーが発生しました'], 500);
		}
	}

//	public function reviewedByUser(string $receiver_id) //ログインユーザーがレビューしたかどうか
//	{
//		try {
//			$sender_id = Auth::id(); // 現在ログインしているユーザーIDを取得
//			$isReviewed = Review::where('receiver_id', $receiver_id)
//								->where('sender_id', $sender_id)
//								->exists(); // 指定した受信者に対するレビューが存在するかチェック
//
//			return response()->json(['isReviewed' => $isReviewed]);
//		} catch (\Exception $e) { // 例外が発生した場合の処理
//
//			Log::error('レビュー状態の確認中にエラーが発生しました: ' . $e->getMessage());
//			return response()->json(['error' => 'レビュー状態の確認中にエラーが発生しました'], 500);
//		}
//	}

	// `ReviewController` 内に新しいメソッドを追加
	public function reviewedByUser($user_id)
	{
		// ユーザーがログインしていない場合はエラーレスポンスを返す
		if (!Auth::check()) {
			return response()->json(['error' => 'ユーザーがログインしていません'], 401);
		}
		$user_id = intval($user_id);
		$logged_in_user_id = Auth::id();

		$isReviewed = Review::where('receiver_id', $user_id)
			->where('sender_id', $logged_in_user_id)
			->exists();

		return response()->json(['isReviewed' => $isReviewed]);
	}
}
