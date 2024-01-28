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
Route::get('/products',                       'ProductController@index')->name('index');       //商品一覧取得
Route::get('/products/prefecture',            'ProductController@prefecture');                 //出品したユーザーの都道府県IDを取得
Route::get('/products/ranking',               'ProductController@ranking');                    //お気に入りが多い５件を取得
Route::get('/products/{id}',                  'ProductController@show')->name('product.show'); //商品情報取得
Route::post('/products',                      'ProductController@store');                      //商品登録
Route::post('/products/{id}',                 'ProductController@update');                     //商品更新
Route::delete('/products/{id}',               'ProductController@destroy');                    //商品削除
Route::post('/products/{id}/purchase',        'ProductController@purchase');                   //商品購入
Route::get('/products/{id}/purchasedByUser',  'ProductController@purchasedByUser');            //商品を購入したかを返す
Route::get('/products/{id}/canceledByUser',   'ProductController@canceledByUser');             //商品をキャンセルしたかを返す
Route::get('/products/{u_id}/{p_id}/other',   'ProductController@otherProducts');              //出品者の他の商品を返す
Route::get('/products/{c_id}/{p_id}/similar', 'ProductController@similarProducts');            //出品者の他の商品を返す
Route::post('/products/{id}/like',            'ProductController@like')->name('product.like'); //お気に入り登録
Route::delete('/products/{id}/unlike',        'ProductController@unlike');                     //お気に入り解除
Route::post('/products/{id}/cancel',          'ProductController@cancel');                     //商品キャンセル
Route::get('/products/{id}/isReviewed',       'ProductController@isReviewed');                 //レビュー投稿済みかどうか

//=================================================================
// User
//=================================================================
Route::get('/users/{id}',                 'UserController@index');          //ユーザー情報取得
Route::get('/users/{id}/shopUser',        'UserController@shopUser');       //コンピニユーザー情報取得
Route::post('/users/{id}/updateProfile',  'UserController@updateProfile');  //プロフィール更新
Route::post('/users/{id}/updatePassword', 'UserController@updatePassword'); //パスワード更新
Route::get('/users/{id}/posted',          'UserController@posted');         //出品した商品一覧
Route::get('/users/{id}/purchased',       'UserController@purchased');      //購入した商品一覧
Route::get('/users/{id}/wasPurchased',    'UserController@wasPurchased');   //購入された商品一覧
Route::get('/users/{id}/liked',           'UserController@liked');          //いいねした商品一覧
Route::get('/users/{id}/canceled',        'UserController@canceled');       //キャンセルした商品一覧
Route::get('/users/{id}/wasCanceled',     'UserController@wasCanceled');    //キャンセルされた商品一覧
Route::get('/users/{id}/reviewed',        'UserController@reviewed');       //レビューしたユーザー一覧
Route::get('/users/{id}/wasReviewed',     'UserController@wasReviewed');    //レビューされたユーザー一覧

//=================================================================
// MyPage
//=================================================================
Route::get('/mypage/liked',        'MyPageController@liked');        //お気に入りした商品5件取得(利用者)
Route::get('/mypage/posted',       'MyPageController@posted');       //投稿した商品を5件取得(コンビニユーザー)
Route::get('/mypage/purchased',    'MyPageController@purchased');    //購入した商品5件取得(利用者)
Route::get('/mypage/wasPurchased', 'MyPageController@wasPurchased'); //購入された商品を5件取得(コンビニユーザー)
Route::get('/mypage/canceled',     'MyPageController@canceled');     //キャンセルした商品5件取得(利用者)
Route::get('/mypage/wasCanceled',  'MyPageController@wasCanceled');  //キャンセルされた商品5件取得(コンビニユーザー)
Route::get('/mypage/reviewed',     'MyPageController@reviewed');     //投稿したレビューを5件取得(利用者)
Route::get('/mypage/wasReviewed',  'MyPageController@wasReviewed');  //投稿されたレビューを5件取得(コンビニユーザー)

//=================================================================
// Review
//=================================================================
Route::post('/reviews',                      'ReviewController@store');
Route::get('/reviews/{r_id}/reviewedByUser', 'ReviewController@reviewedByUser');
Route::get('/reviews/{r_id}/otherProducts',  'ReviewController@otherProducts');
Route::get('/reviews/{s_id}/{r_id}',         'ReviewController@show');
Route::post('/reviews/update',               'ReviewController@update');
Route::delete('/reviews/{s_id}/{r_id}',      'ReviewController@destroy');

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

