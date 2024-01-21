<?php

namespace App;

use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group', 'name', 'branch', 'prefecture_id', 'address',
	    'email', 'password', 'image', 'introduce', 'deleted_at',
	    'created_at', 'updated_at'
    ];

    protected $appends = ['prefecture_name'];

	protected $visible = [
		'id', 'group', 'name', 'branch', 'prefecture_id', 'address',
		'email', 'email_verified_at', 'image', 'introduce', 'deleted_at',
		'created_at', 'updated_at',
		'prefecture_name'
	];

//    /**
//     * The attributes that should be hidden for arrays.
//     *
//     * @var array
//     */
//    protected $hidden = [
//        'password',
//	    'remember_token',
//    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	/**
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token) //パスワードリセット通知の送信をオーバーライド
	{
		$this->notify(new PasswordResetNotification($token));
	}

	/*
	 * --------------------------------------------------
	 * アクセサ
	 * --------------------------------------------------
	 */
	/**
	 * アクセサ - prefecture_name
	 * @return string
	 */
	public function getPrefectureNameAttribute()
	{
		if($this->group === 1) {
			return false;
		}
		return $this->prefecture->name;
	}

	//====================================================
    //リレーション
	//====================================================
	public function prefecture() //都道府県テーブル
	{
		return $this->belongsTo('App\Prefecture');
	}

	public function products() //商品テーブル
	{
		return $this->hasMany('App\Product');
	}

	public function likes() //お気に入りテーブル
	{
		return $this->belongsToMany('App\Product', 'likes')
					->withTimestamps();
	}

	public function buyerHistories() //購入履歴テーブル（利用者）
	{
		return $this->hasMany('App\History', 'buyer_id');
	}

	public function sellerHistories() //購入履歴テーブル（出品者）
	{
		return $this->hasMany('App\History', 'seller_id');
	}

	public function cancels() //購入キャンセルテーブル
	{
		return $this->hasMany('App\Cancel', 'cancel_user_id');
	}

	public function senderReviews() //レビューテーブル(利用者)
	{
		return $this->hasMany('App\Review', 'sender_id');
	}

	public function receiverReviews() //レビューテーブル(コンビニ)
	{
		return $this->hasMany('App\Review', 'receiver_id');
	}
}
