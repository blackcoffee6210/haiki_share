<?php

namespace App;

use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
	    'email', 'password', 'image', 'introduce', 'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	/**
	 * パスワードリセット通知の送信をオーバーライド
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new PasswordResetNotification($token));
	}

	//===============================================
    //リレーション
	//===============================================
	public function products() {
		return $this->hasMany('App\Product');
	}

	public function prefecture()
	{
		return $this->belongsTo('App\Prefecture');
	}

	public function likes()
	{
		return $this->belongsToMany('App\Product', 'likes')
					->withTimestamps();
	}

	public function histories()
	{
		return $this->belongsToMany('App\Product', 'histories')
					->withTimestamps();
	}
}
