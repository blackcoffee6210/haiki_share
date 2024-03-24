<?php

namespace App\Http\Controllers;

use App\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
	public function checkPurchased($productId) //ユーザーの購入履歴を確認
	{
		try {
			$userId = Auth::id(); // ログインユーザーのIDを取得
			$purchased = History::where('buyer_id', $userId)
								->where('product_id', $productId)
								->exists(); // 購入履歴が存在するか確認

			return response()->json(['purchased' => $purchased]);

		}catch (\Exception $e) {
			Log::error('購入履歴の確認中にエラーが発生しました。', ['error' => $e->getMessage()]);
			return response()->json(['error' => '購入履歴の確認中に問題が発生しました。'], 500);
		}
	}
}
