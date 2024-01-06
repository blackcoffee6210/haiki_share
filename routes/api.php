<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//=================================================================
// Auth
//=================================================================
Route::post('/register',       'Auth\RegisterController@register')->name('register');                       //ユーザー登録
Route::post('/login',          'Auth\LoginController@login')->name('login');                                //ログイン
Route::post('/logout',         'Auth\LoginController@logout')->name('logout');                              //ログアウト
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email'); //パスワードリセットメール送信
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');               //パスワードリセット処理

//=================================================================
// Product
//=================================================================
Route::get('/products',                'ProductController@index');     //商品一覧取得
Route::get('/products/{id}',           'ProductController@show');      //商品情報取得
Route::post('/products',               'ProductController@store');     //商品登録
Route::post('/products/{id}/update',   'ProductController@update');    //商品更新
Route::delete('/products/{id}',        'ProductController@destroy');   //商品削除      todo: 処理実装
Route::post('/products/{id}/purchase', 'ProductController@purchase');  //商品購入
Route::post('/products/{id}/like',     'ProductController@like');      //お気に入り登録 todo: LikeController作成後、切り出す
Route::delete('/products/{id}/unlike', 'ProductController@unlike');    //お気に入り解除 todo: LikeController作成後、切り出す
Route::post('/products/{id}/cancel',   'ProductController@cancel');    //商品キャンセル

//=================================================================
// User
//=================================================================
Route::get('/users/{id}',                 'UserController@index');          //ユーザー情報取得
Route::get('/users/{id}/shopUser',        'UserController@shopUser');       //コンピニユーザー情報取得
Route::post('/users/{id}/updateProfile',  'UserController@updateProfile');  //プロフィール更新
Route::post('/users/{id}/updatePassword', 'UserController@updatePassword'); //パスワード更新
Route::get('/users/{id}/posted',          'UserController@posted');         //出品した商品一覧
Route::get('/users/{id}/purchased',       'UserController@purchased');      //購入した商品一覧
Route::get('/users/{id}/liked',           'UserController@liked');          //いいねした商品一覧
Route::get('/users/{id}/canceled',        'UserController@canceled');       //キャンセルした商品一覧
Route::get('/users/{id}/reviewed',        'UserController@reviewed');       //レビュー一覧

//=================================================================
// Review
//=================================================================
Route::post('/reviews',             'ReviewController@store');   //レビュー登録
Route::post('/reviews/{id}/update', 'ReviewController@update');  //レビュー編集 todo: 処理実装
Route::delete('/reviews/{id}',      'ReviewController@destroy'); //レビュー削除 todo: 処理実装


//=================================================================
// Other
//=================================================================
Route::get('/refresh-token', function(Request $request) { //トークンリフレッシュ
	$request->session()->regenerateToken();
	return response()->json();
});
Route::get('/user', function() { return Auth::user(); })->name('user'); //ログインユーザーを返す
Route::get('/categories',      'CategoryController');                   //カテゴリー取得API
Route::get('/prefectures',     'PrefectureController');                 //都道府県取得API
Route::get('/recommendations', 'RecommendationController');             //ユーザー評価取得API

