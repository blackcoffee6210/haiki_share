<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    //ソフトデリート
	use SoftDeletes;

	protected $fillable = [
		'user_id', 'category_id', 'image', 'name', 'detail',
		'price', 'expire', 'deleted_at'
	];

	//$perPageは、paginate()のデフォルト件数を変更するプロパティ
	protected $perPage = 12;

	//JSONに含めるアクセサ
	//アクセサとは、DBから取得したデータを自動的に加工することができる機能
	//ユーザー定義のアクセサをJSONに含めるには、明示的に$appendプロパティに登録する
	protected $appends = [
		'user_image', 'user_name', 'category_name', 'likes_count',
		'liked_by_user', 'purchased_by_user', 'is_purchased',
	];

	//$visibleはJSONに含める属性を定義する
	//$hiddenはその逆(含めたくないものを登録する)
	protected $visible = [
		'id', 'user_id', 'category_id', 'image', 'name', 'detail', 'price', 'expire',
		'deleted_at', 'created_at', 'updated_at',
		'user_image', 'user_name', 'category_name', 'likes_count',
		'liked_by_user', 'purchased_by_user', 'is_purchased',
	];

	//=====================================================
	// アクセサ
	//=====================================================
	/**
	 * アクセサ - user_name
	 * @return string
	 */
	//ユーザー名を取得する
	public function getUserNameAttribute()
	{
		return $this->user->name;
	}
	/**
	 * アクセサ - user_image
	 * @string
	 */
	//ユーザー画像を取得する
	public function getUserImageAttribute()
	{
		return $this->user->image;
	}
	/**
	 * アクセサ - category_name
	 * @string
	 */
	//商品カテゴリー名を取得する
	public function getCategoryNameAttribute()
	{
		return $this->category->name;
	}
	/**
	 * アクセサ - likes_count
	 * @return int
	 */
	//いいね数を取得するアクセサ
	public function getLikesCountAttribute()
	{
		return $this->likes->count();
	}
	/**
	 * アクセサ - liked_by_user
	 * @return boolean
	 */
	//リクエストしたユーザーがいいねしているかどうかを取得するアクセサ
	//trueまたはfalseを返す
	public function getLikedByUserAttribute()
	{
		//ユーザーがゲストの場合(ログインしていなければ)falseを返す
		if(Auth::guest() ) {
			return false;
		}
		//Laravelのコレクションメソッドcontainを使って、
		//ログインユーザーのIDと合致するいいねが含まれるか調べる
		return $this->likes->contains(function($user) {
			//一致したらtrueを返す
			return $user->id === Auth::user()->id;
		});
	}
	/**
	 * アクセサ - purchased_by_user
	 * @return boolean
	 */
	//リクエストしたユーザーが商品を購入済みかどうかを取得する
	public function getPurchasedByUserAttribute()
	{
		//ユーザーがゲストの場合(ログインしていなければ)falseを返す
		if(Auth::guest()) {
			return false;
		}
		//historiesテーブルのuser_idとログインユーザーidが合致するか調べる
		return $this->histories->contains(function($user) {
			//idが一致したらtrueを返す
			return $user->id === Auth::user()->id;
		});
	}
	/**
	 * アクセサ - is_purchased
	 * @return boolean
	 */
	//商品が購入されているかどうかを真偽値で返すアクセサ
	public function getIsPurchasedAttribute()
	{
		//historiesテーブルにカウントがある(=購入されている)のでtrueを返す
		return ($this->histories->count()) ? true : false;
	}


	//======================================================
	//リレーション
	//======================================================
	public function user() {
		return $this->belongsTo('App\User');
	}
	public function category() {
		return $this->belongsTo('App\Category');
	}
	//likesテーブルを中間テーブルとしたproductsテーブルとusersテーブルの多対多の関係を表している
	//今回はlikesテーブルに当たるモデルクラスは作成しない
	//特に外部キーしか中身のない中間テーブルの場合はモデルクラスは作成する必要のない場合が多い
	//laravelのリレーション機能を使えば、関連するモデルから間接的に中間テーブルを操作できる
	public function likes()
	{
		return $this->belongsToMany('App\User', 'likes')
					->withTimestamps();
		//withTimestampsは、このリレーションメソッドを使ってlikesテーブルにデータを挿入したとき、
		//created_atおよびupdated_atカラムを更新させるための指定の仕方
	}
	public function histories()
	{
		return $this->belongsToMany('App\User', 'histories')
					->withTimestamps();
	}
}
