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

    public function liked(string $id) //お気に入りした商品を5件取得
    {
    	return User::find($id)
		           ->likes()
		           ->orderByDesc('likes.created_at')
		           ->take(3)
		           ->get();
    }

	public function purchased(string $id) //購入した商品を5件取得
	{
		$products = Product::whereHas('history', function($q) {
						$q->where('buyer_id', '=', Auth::id());
					})->take(3)->get();
		return $products;
	}

	public function wasPurchased(string $id) //購入された商品を5件取得
	{

	}

	public function canceled(string $id) //キャンセルした商品を5件取得
	{
		return User::find($id)
				   ->cancels()
				   ->orderByDesc('cancels.created_at')
				   ->take(3)
				   ->get();
	}

	public function reviewedShopUser(string $id) //レビューしたコンビニユーザーを5件取得
	{
		return User::find($id)
				   ->senderReviews()
				   ->orderByDesc('reviews.created_at')
				   ->take(3)
				   ->get();
	}

	public function posted(string $id) //投稿した商品を5件取得
	{
		return User::find($id)
				   ->products()
				   ->orderByDesc('products.created_at')
				   ->take(3)
				   ->get();
	}

















}
