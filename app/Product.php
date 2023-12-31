<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
		'user_image', 'user_name', 'category_name'
	];

	//$visibleはJSONに含める属性を定義する
	//$hiddenはその逆(含めたくないものを登録する)
	protected $visible = [
		'id', 'user_id', 'category_id', 'image', 'name', 'detail', 'price', 'expire',
		'deleted_at', 'created_at', 'updated_at',
		'user_image', 'user_name', 'category_name'
	];

	//=====================================================
	// アクセサ
	//=====================================================
	/**
	 * @return string
	 */
	//ユーザー名を取得する
	public function getUserNameAttribute()
	{
		return $this->user->name;
	}
	/**
	 * @string
	 */
	//ユーザー画像を取得する
	public function getUserImageAttribute()
	{
		return $this->user->image;
	}
	/**
	 * @string
	 */
	//商品カテゴリー名を取得する
	public function getCategoryNameAttribute()
	{
		return $this->category->name;
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
}
