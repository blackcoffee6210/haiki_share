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
Route::get('/products', 'ProductController@index'); //商品一覧取得
Route::get('/products/{id}', 'ProductController@show'); //商品詳細
Route::post('/products', 'ProductController@store'); //商品登録
Route::post('/products/{id}/purchase', 'ProductController@purchase'); //商品購入

Route::post('/products/{id}/like', 'ProductController@like'); //お気に入り登録
Route::delete('/products/{id}/like', 'ProductController@unlike'); //お気に入り解除
Route::get('/categories', 'CategoryController'); //カテゴリー取得API
Route::get('/prefectures', 'PrefectureController'); //都道府県取得API
