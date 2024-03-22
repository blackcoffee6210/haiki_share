<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
	protected $fillable = [
		'sender_id', 'receiver_id', 'recommendation_id',
		'title', 'detail'
		];

	//ユーザー画像、ユーザー名、投稿日、ユーザー評価、タイトル、コメント
	//JSONに含めるアクセサ
	protected $appends = [
		'sender_name', 'sender_image', 'receiver_name', 'receiver_image', 'receiver_branch', 'recommend',
	];

	//$visibleはJSONに含める属性を定義する
	protected $visible = [
		'id', 'sender_id', 'receiver_id', 'recommendation_id', 'title', 'detail', 'created_at', 'updated_at',
		'sender_name', 'receiver_name', 'sender_image', 'receiver_image', 'receiver_branch', 'recommend',
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
	 * アクセサ - receiver_branch
	 * @return string
	 */
	public function getReceiverBranchAttribute() //出品者の支店名を取得する
	{
		return $this->receiver->branch;
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
