<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function __invoke() {
    	try {
    		$categories = Category::all(); //カテゴリーをすべて取得
		    return $categories;
	    }catch (\Exception $e) {
    		Log::error('カテゴリの取得に失敗しました： '. $e->getMessage());
		    return response()->json(['error' => 'カテゴリの取得に失敗しました。'], 500);
	    }
    }
}
