<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
	use SoftDeletes; //ソフトデリート

	protected $fillable = [
		'user_id', 'category_id', 'image', 'name', 'detail',
		'price', 'expire', 'deleted_at', 'created_at', 'updated_at'
	];

	protected $perPage = 12; //$perPageは、paginate()のデフォルト件数を変更するプロパティ

	//JSONに含めるアクセサ
	//アクセサとは、DBから取得したデータを自動的に加工することができる機能
	//ユーザー定義のアクセサをJSONに含めるには、明示的に$appendプロパティに登録する
	protected $appends = [
		'user_image', 'user_name', 'email', 'branch', 'category_name',
		'likes_count', 'liked_by_user',
		'is_my_product',
        'is_purchased',
		'is_canceled',
	    'cancels_count'
	];

	//$visibleはJSONに含める属性を定義する
	//$hiddenはその逆(含めたくないものを登録する)
	protected $visible = [
		'id','user_id', 'category_id', 'image', 'name', 'detail', 'price',
		'expire', 'deleted_at', 'created_at', 'updated_at',
		'user_image', 'user_name', 'email', 'branch', 'category_name',
		'likes_count', 'liked_by_user',
		'is_my_product',
		'is_purchased',
		'is_canceled',
		'cancels_count'
	];

	//=====================================================
	// アクセサ
	//=====================================================
	/**
	 * アクセサ - user_name
	 * @return string
	 */
	public function getUserNameAttribute() //ユーザー名を取得する
	{
		return $this->user->name;
	}
	/**
	 * アクセサ - email
	 * @return string
	 */
	public function getEmailAttribute() //ユーザーのEmailを取得する
	{
		return $this->user->email;
	}
	/**
	 * アクセサ - user_name
	 * @return string
	 */
	public function getBranchAttribute() //支店名を取得する
	{
		return $this->user->branch;
	}
	/**
	 * アクセサ - user_image
	 * @string
	 */
	public function getUserImageAttribute() //ユーザー画像を取得する
	{
		return $this->user->image;
	}
	/**
	 * アクセサ - category_name
	 * @return string
	 */
	public function getCategoryNameAttribute() //商品カテゴリー名を取得する
	{
		return $this->category->name;
	}
	/**
	 * アクセサ - is_my_product
	 * @return boolean
	 */
	public function getIsMyProductAttribute() //自分の商品かどうかを真偽値で返す
	{
		if(Auth::guest()) { //ユーザーがゲストの場合(ログインしていなければ)falseを返す
			return false;
		}
		return ($this->user_id === Auth::user()->id) ? true : false; //商品のユーザーidとログインidが同じであればtrueを返す
	}
	/**
	 * アクセサ - likes_count
	 * @return int
	 */
	public function getLikesCountAttribute() //いいね数を取得するアクセサ
	{
		return $this->likes->count();
	}
	/**
	 * アクセサ - liked_by_user
	 * @return boolean
	 */
	public function getLikedByUserAttribute() //リクエストしたユーザーがいいねしているかどうかを取得するアクセサ
	{
		if(Auth::guest() ) { //ユーザーがゲストの場合(ログインしていなければ)falseを返す
			return false;
		}
		return $this->likes->contains(function($user) { //Laravelのコレクションメソッドcontainを使って、ログインユーザーのIDと合致するいいねが含まれるか調べる
			return $user->id === Auth::user()->id; //一致したらtrueを返す
		});
	}
	/**
	 * アクセサ - is_purchased
	 * @return boolean
	 */
	public function getIsPurchasedAttribute() //商品が購入されているかどうかを真偽値で返すアクセサ
	{
		return ($this->history()->count() ) ? true : false; //historiesテーブルにカウントがある(=購入されている)のでtrueを返す
	}
	/**
	 * アクセサ - is_canceled
	 * @return boolean
	 */
	public function getIsCanceledAttribute() //商品がキャンセルされているかどうかを真偽値で返すアクセサ
	{
		return ($this->cancels->count()) ? true : false; //historiesテーブルにカウントがある(=購入されている)のでtrueを返す
	}
	/**
	 * アクセサ - cancels_count
	 * @return int
	 */
	public function getCancelsCountAttribute() //キャンセル数を取得するアクセサ
	{
		return $this->cancels->count();
	}

	//======================================================
	//リレーション
	//======================================================
	public function user() //ユーザーテーブル
	{
		return $this->belongsTo('App\User');
	}

	public function category() //カテゴリーテーブル
	{
		return $this->belongsTo('App\Category', 'category_id');
	}

	public function likes() //お気に入りテーブル
	{
		//likesテーブルを中間テーブルとしたproductsテーブルとusersテーブルの多対多の関係を表している
		//今回はlikesテーブルに当たるモデルクラスは作成しない
		//特に外部キーしか中身のない中間テーブルの場合はモデルクラスは作成する必要のない場合が多い
		//laravelのリレーション機能を使えば、関連するモデルから間接的に中間テーブルを操作できる
		return $this->belongsToMany('App\User', 'likes')
					->withTimestamps();
		//withTimestampsは、このリレーションメソッドを使ってlikesテーブルにデータを挿入したとき、
		//created_atおよびupdated_atカラムを更新させるための指定の仕方
	}


	public function history() //購入履歴テーブル
	{
		return $this->hasOne('App\History');
	}

	public function cancels() //購入キャンセルテーブル
	{
		return $this->hasMany('App\Cancel');
	}
}
