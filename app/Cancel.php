<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cancel extends Model
{
    protected $fillable = ['cancel_user_id', 'post_user_id', 'product_id'];

    public function cancelUser() //キャンセルした利用者のリレーション
    {
    	return $this->belongsTo('App\User', 'cancel_user_id');
    }

	public function postUser() //出品したコンビニユーザーのリレーション
	{
		return $this->belongsTo('App\User', 'post_user_id');
	}

	public function product() //商品のリレーション
	{
		return $this->belongsTo('App\Product');
	}
}
