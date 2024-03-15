<?php

namespace App\Http\Controllers;

use App\Prefecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrefectureController extends Controller
{
	public function __invoke()
    {
    	try {
    		$prefectures = Prefecture::all(); //都道府県を全件取得
    		return $prefectures;
	    }catch(\Exception $e) {
    		Log::error('都道府県の取得に失敗しました: '. $e->getMessage());
    		return response()->json(['error', '都道府県の取得に失敗しました'], 500);
	    }
    }
}
