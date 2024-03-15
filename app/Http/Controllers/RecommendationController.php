<?php

namespace App\Http\Controllers;

use App\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
    public function __invoke()
    {
    	try {
    		$recommendations = Recommendation::all(); //ユーザーの評価「とてもオススメ」「オススメ」「オススメしない」を取得
		    return $recommendations;
	    }catch(\Exception $e) {
    		Log::error('ユーザー評価の取得に失敗しました: '. $e->getMessage());
    		return response()->json(['error', 'ユーザー評価の取得に失敗しました'],500);
	    }
    }
}
