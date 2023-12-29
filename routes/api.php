<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


Route::post('/register', 'Auth\RegisterController@register')->name('register'); //ユーザー登録
Route::post('/login',    'Auth\LoginController@login')->name('login'); //ログイン
Route::post('/logout',   'Auth\LoginController@logout')->name('logout'); //ログアウト
Route::get('/refresh-token', function(Request $request) { //トークンリフレッシュ
	$request->session()->regenerateToken();
	return response()->json();
});
Route::get('/user', function() { return Auth::user(); })->name('user'); //ログインユーザーを返す
Route::post('/products', 'ProductController@store'); //商品登録
Route::get('/category', 'CategoryController'); //カテゴリー取得API
Route::get('/prefecture', 'PrefectureController'); //都道府県取得API
