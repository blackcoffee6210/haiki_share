<?php

namespace App\Http\Controllers;

use App\History;
use App\Product;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyPageController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth'); //認証
	}

    public function liked() //お気に入りした商品を5件取得
    {
    	return User::find(Auth::id())
		           ->likes()
		           ->orderByDesc('likes.created_at')
		           ->take(5)
		           ->get();
    }

	public function posted() //投稿した商品を5件取得
	{
		return User::find(Auth::id())
				   ->products()
				   ->orderByDesc('products.updated_at')
				   ->take(5)
				   ->get();
	}

	public function purchased() //購入した商品を5件取得
	{
		$products = Product::whereHas('history', function($q) {
						$q->where('buyer_id', '=', Auth::id());
					})->take(5)->get();
		return $products;
	}

	public function wasPurchased() //購入された商品を5件取得
	{
		$products = Product::whereHas('history', function($q) {
						$q->where('seller_id', Auth::id());
					})->take(5)->get();
		return $products;
	}

	public function canceled() //キャンセルした商品を5件取得
	{
		$products = Product::whereHas('cancels', function($q) {
						$q->where('cancel_user_id', Auth::id());
					})->take(5)->get();
		return $products;
	}

	public function wasCanceled() //キャンセルされた商品を5件取得
	{
		$products = Product::whereHas('cancels', function($q) {
						$q->where('post_user_id', Auth::id());
					})->take(5)->get();
		return $products;
	}

	public function deleted() //削除した商品を5件取得
	{
		$products = Product::where('user_id', Auth::id())
						   ->onlyTrashed()
						   ->orderByDesc('deleted_at')
						   ->take(5)
						   ->get();
		return $products;
	}

	public function reviewed() //投稿したレビューを5件取得（利用者）
	{
		return User::find(Auth::id())
				   ->senderReviews()
				   ->orderByDesc('reviews.created_at')
				   ->take(5)
				   ->get();
	}

	public function wasReviewed() //投稿されたレビューを5件取得（コンビニ）
	{
		return User::find(Auth::id())
					->receiverReviews()
					->orderByDesc('reviews.created_at')
					->take(5)
					->get();
	}



















}
