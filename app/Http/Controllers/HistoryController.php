<?php

namespace App\Http\Controllers;

use App\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
	public function checkPurchased($productId) //ユーザーの購入履歴を確認
	{
		$userId = Auth::id(); // ログインユーザーのIDを取得
		$purchased = History::where('buyer_id', $userId)
							->where('product_id', $productId)
							->exists(); // 購入履歴が存在するか確認

		return response()->json(['purchased' => $purchased]);
	}
}
