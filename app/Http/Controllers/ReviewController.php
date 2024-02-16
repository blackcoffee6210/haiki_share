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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
		$review = Review::with(['sender', 'receiver', 'recommendation'])
						->where('sender_id', $s_id)
						->where('receiver_id', $r_id)
						->get();
		return $review ?? abort(404); //レビューが見つからなかったら404を返す
	}

	public function store(StoreReview $request) //レビュー投稿
	{
		$recommendation = Recommendation::find($request->recommendation_id); //ユーザー評価(name)を取得

		$review         = new Review;                  //インスタンス生成
		$sender_email   = Auth::user()->email;         //送信者のEmail
		$receiver_email = $request->shopUser['email']; //受信者のEmail

		$review->sender_id         = $request->sender_id;         //送信者のID
		$review->receiver_id       = $request->receiver_id;       //受信者のID
		$review->recommendation_id = $request->recommendation_id; //ユーザー評価のID
		$review->title             = $request->title;             //レビュータイトル
		$review->detail            = $request->detail;            //レビューの内容
		$review->save();

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

		return response($review, 201);
	}

	public function update(UpdateReview $request) //レビュー更新
	{
		$review = Review::with(['sender', 'receiver', 'recommendation'])
						->where('sender_id', $request->sender_id)
						->where('receiver_id', $request->receiver_id)
						->update([
							'recommendation_id' => $request->recommendation_id,
							'title'             => $request->title,
							'detail'            => $request->detail
						]);
		//レビュー更新処理はメールを送らない

		return response($review, 200);
	}

	public function destroy(string $s_id, string $r_id) //レビュー削除
	{
		$review = Review::where('sender_id', $s_id)
						->where('receiver_id', $r_id)
						->delete();
		return response($review, 200);
	}

	public function otherProducts(string $r_id) //出品した商品を取得
	{
		return Product::where('user_id', $r_id)
			->orderByDesc('created_at')
			->get();
	}

	public function topPageReview() //topページに表示するレビューをランダムで3件取得
	{
		return Review::inRandomOrder()->take(3)->get();
	}

	public function reviewedByUser(string $r_id) //ログインユーザーがレビューしたかどうか
	{
		$isReviewed = Review::where('receiver_id', $r_id)
							->where('sender_id', Auth::id())
							->get();
		return $isReviewed;
	}
}





















