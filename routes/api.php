<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//ユーザー登録
Route::post('/register', 'Auth\RegisterController@register')->name('register');
//ログイン
Route::post('/login', 'Auth\LoginController@login')->name('login');
//ログアウト
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');
//トークンリフレッシュ
Route::get('/refresh-token', function(Request $request) {
	$request->session()->regenerateToken();
	return response()->json();
});
