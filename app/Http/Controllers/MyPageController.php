<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MyPageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth'); //認証
	}

	protected $errorMessages = [ // エラーメッセージをプロパティとして定義
		'liked'        => 'お気に入り商品の取得に失敗しました。',
		'posted'       => '出品した商品の取得に失敗しました。',
		'purchased'    => '購入した商品の取得に失敗しました。',
		'wasPurchased' => '購入された商品の取得に失敗しました。',
		'canceled'     => 'キャンセルした商品の取得に失敗しました。',
		'wasCanceled'  => 'キャンセルされた商品の取得に失敗しました。',
		'deleted'      => '削除した商品の取得に失敗しました。',
		'reviewed'     => '投稿したレビューの取得に失敗しました。',
		'wasReviewed'  => '投稿されたレビューの取得に失敗しました。',
	];

	private function getUserOrFail() // ユーザーの取得または例外を投げる
	{
		$user = Auth::user();
		if(!$user) {
			throw new \Exception('ユーザーが見つかりません。 ', Response::HTTP_NOT_FOUND);
		}
		return $user;
	}

	private function handleException(\Throwable $e, $messageKey) // エラーハンドリングの共通化
	{
		Log::error("{$this->errorMessages[$messageKey]}: {$e->getMessage()}");
		return response()->json(['error' => $this->errorMessages[$messageKey]], Response::HTTP_INTERNAL_SERVER_ERROR);
	}

	public function liked() //お気に入りした商品を5件取得（利用者）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->likes()->with('product')->orderByDesc('likes.created_at')->take(5)->get();
			return response($products, 200);

		} catch (\Exception $e) {
			return $this->handleException($e, 'liked');
		}
	}

	public function posted() //出品した商品を5件取得（コンビニユーザー）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->products()->orderByDesc('updated_at')->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'posted');
		}
	}

	public function purchased() //購入した商品を5件取得（利用者）
	{
		try {
			$user = $this->getUserOrFail();
			$products = Product::whereHas('history', function($q) {
				$q->where('buyer_id', '=', Auth::id());
			})->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'purchased');
		}
	}

	public function wasPurchased() //購入された商品を5件取得（コンビニユーザー）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->products()->whereHas('history', function($q) use ($user) {
				$q->where('seller_id', $user->id);
			})->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'wasPosted');
		}
	}

	public function canceled() //キャンセルした商品を5件取得（利用者）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->products()->whereHas('cancels', function($q) use ($user) {
				$q->where('cancel_user_id', $user->id);
			})->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'canceled');
		}
	}

	public function wasCanceled() //キャンセルされた商品を5件取得（コンビニユーザー）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->products()->whereHas('cancels', function($q) use ($user) {
				$q->where('post_user_id', $user->id);
			})->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'wasCanceled');
		}
	}

	public function deleted() //削除した商品を5件取得（コンビニユーザー）
	{
		try {
			$user = $this->getUserOrFail();
			$products = $user->products()->onlyTrashed()->orderByDesc('deleted_at')->take(5)->get();
			return response($products, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'deleted');
		}
	}

	public function reviewed() //投稿したレビューを5件取得（利用者）
	{
		try {
			$user = $this->getUserOrFail();
			$reviews = $user->senderReviews()->orderByDesc('created_at')->take(5)->get();
			return response($reviews, 200);

		}catch (\Exception $e) {
			return $this->handleException($e, 'reviewed');
		}
	}

	public function wasReviewed() //投稿されたレビューを5件取得（コンビニユーザー）
	{
		try {
			$user = $this->getUserOrFail();
			$reviews = $user->receiverReviews()->orderByDesc('created_at')->take(5)->get();
			return response($reviews, 200);

		}catch(\Exception $e) {
			return $this->handleException($e, 'wasReviewed');
		}
	}
}
