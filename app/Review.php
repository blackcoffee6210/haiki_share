<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use softDeletes; //ソフトデリート

	protected $fillable = [
		'sender_id', 'receiver_id', 'recommendation_id',
		'title', 'detail', 'deleted_at'
		];

	//ユーザー画像、ユーザー名、投稿日、ユーザー評価、タイトル、コメント
	//JSONに含めるアクセサ
	protected $appends = [
		'sender_name', 'sender_image', 'receiver_name', 'receiver_image',
		'recommend'
	];

	//$visibleはJSONに含める属性を定義する
	protected $visible = [
		'id', 'sender_id', 'receiver_id', 'recommendation_id', 'title', 'detail',
		'deleted_at', 'created_at', 'updated_at',
		'sender_name', 'receiver_name', 'sender_image', 'receiver_image',
		'recommend'
	];

	//======================================================
	//アクセサ
	//======================================================
	/**
	 * アクセサ - sender_name
	 * @return string
	 */
	public function getSenderNameAttribute() //利用者のユーザー名を取得する
	{
		return $this->sender->name;
	}
	/**
	 * アクセサ - receiver_name
	 * @return string
	 */
	public function getReceiverNameAttribute() //出品者のユーザー名を取得する
	{
		return $this->receiver->name;
	}
	/**
	 * アクセサ - sender_image
	 * @return string
	 */
	public function getSenderImageAttribute() //利用者のユーザー画像を取得する
	{
		return $this->sender->image;
	}
	/**
	 * アクセサ - receiver_image
	 * @return string
	 */
	public function getReceiverImageAttribute() //出品者のユーザー画像を取得する
	{
		return $this->receiver->image;
	}
	/**
	 * アクセサ - recommend
	 * @return string
	 */
	public function getRecommendAttribute() //ユーザー評価を取得する
	{
		return $this->recommendation->name;
	}


	//======================================================
	//リレーション
	//======================================================
	public function sender() //ユーザーテーブル（利用者）
	{
		return $this->belongsTo('App\User', 'sender_id');
	}

	public function receiver() //ユーザーテーブル（出品者）
	{
		return $this->belongsTo('App\User', 'receiver_id');
	}

	public function recommendation() //ユーザー評価テーブル
	{
		return $this->belongsTo('App\Recommendation');
	}
}
