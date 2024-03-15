<?php

namespace App\Http\Controllers;

use App\History;
use App\Product;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MyPageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth'); //認証
	}

	public function liked() //お気に入りした商品を5件取得（利用者）
	{
		try {
			$user = User::find(Auth::id());
			if (!$user) { //取得できなかったら
				return response()->json(['error' => 'ユーザーが見つかりません。'], 404);
			}
			return $user->likes()->orderByDesc('likes.created_at')->take(5)->get();
		} catch (\Exception $e) {
			Log::error('お気に入り商品の取得に失敗しました。: ' . $e->getMessage());
			return response()->json(['error' => 'お気に入り商品の取得に失敗しました。'], 500);
		}
	}

	public function posted() //出品した商品を5件取得（コンビニユーザー）
	{
		try {
			$user = User::find(Auth::id());
			if(!$user) {
				return response()->json(['error' => 'ユーザーが見つかりません。', 404]);
			}
			return $user->products()->orderByDesc('products.updated_at')->take(5)->get();
		}catch(\Exception $e) {
			Log::error('出品した商品の取得に失敗しました: '. $e->getMessage());
			return response()->json(['error', '出品した商品の取得に失敗しました。'], 500);
		}
	}

	public function purchased() //購入した商品を5件取得（利用者）
	{
		try {
			$products = Product::whereHas('history', function($q) {
				$q->where('buyer_id', '=', Auth::id());
			})->take(5)->get();
			return $products;
		}catch(\Exception $e) {
			Log::error('購入した商品の取得に失敗しました: '. $e->getMessage());
			return response()->json(['error', '購入した商品の取得に失敗しました。'], 500);
		}
	}

	public function wasPurchased() //購入された商品を5件取得（コンビニユーザー）
	{
		try {
			$products = Product::whereHas('history', function($q) {
				$q->where('seller_id', Auth::id());
			})->take(5)->get();
			return $products;
		}catch(\Exception $e) {
			Log::error('購入された商品の取得に失敗しました。'. $e->getMessage());
			return response()->json(['error', '購入された商品の取得に失敗しました。'], 500);
		}

	}

	public function canceled() //キャンセルした商品を5件取得（利用者）
	{
		try {
			$products = Product::whereHas('cancels', function($q) {
				$q->where('cancel_user_id', Auth::id());
			})->take(5)->get();
			return $products;
		}catch(\Exception $e) {
			Log::error('キャンセルした商品の取得に失敗しました。'. $e->getMessage());
			return response()->json(['error', 'キャンセルした商品の取得に失敗しました。'], 500);
		}
	}

	public function wasCanceled() //キャンセルされた商品を5件取得（コンビニユーザー）
	{
		try {
			$products = Product::whereHas('cancels', function($q) {
				$q->where('post_user_id', Auth::id());
			})->take(5)->get();
			return $products;
		}catch(\Exception $e) {
			Log::error('キャンセルされた商品の取得に失敗しました。'. $e->getMessage());
			return response()->json(['error', 'キャンセルされた商品の取得に失敗しました。'], 500);
	}

	}

	public function deleted() //削除した商品を5件取得（コンビニユーザー）
	{
		try {
			$products = Product::where('user_id', Auth::id());
			if(!$products) {
				return response()->json(['error' => '削除した商品が見つかりません。'], 404);
			}
			return $products->onlyTrashed()->orderByDesc('deleted_at')->take(5)->get();
		}catch(\Exception $e) {
			Log::error('削除した商品の取得に失敗しました。'. $e->getMessage());
			return response()->json(['error', '削除した商品の取得に失敗しました。'], 500);
		}
	}

	public function reviewed() //投稿したレビューを5件取得（利用者）
	{
		try {
			$user = User::find(Auth::id());
			if(!$user) {
				return response()->json(['error', 'ユーザーが見つかりません。'], 404);
			}
			return $user->senderReviews()->orderByDesc('reviews.created_at')->take(5)->get();
		}catch (\Exception $e) {
			Log::error('投稿したレビューの取得に失敗しました。'. $e->getMessage());
			return response()->json(['error', '投稿したレビューの取得に失敗しました。'], 500);
		}
	}

	public function wasReviewed() //投稿されたレビューを5件取得（コンビニユーザー）
	{
		try {
			$user = User::find(Auth::id());
			if(!$user) {
				return response()->json(['error', 'ユーザーが見つかりません。'], 404);
			}
			return $user->receiverReviews()->orderByDesc('reviews.created_at')->take(5)->get();
		}catch(\Exception $e) {
			Log::error('投稿されたレビューの取得に失敗しました： '. $e->getMessage());
			return response()->json(['error', '投稿されたレビューの取得に失敗しました。'], 500);
		}
	}



















}
