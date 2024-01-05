<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use softDeletes; //ソフトデリート

	protected $fillable = [
		'sender_id', 'receiver_id', 'recommendation',
		'title', 'detail', 'deleted_at'
		];

	//======================================================
	//リレーション
	//======================================================
	public function user() //ユーザーテーブル
	{
		return $this->belongsTo('App\User');
	}
	public function recommendation() //ユーザー評価テーブル
	{
		return $this->belongsTo('App\Recommendation');
	}
}
