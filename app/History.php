<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = ['buyer_id', 'seller_id', 'product_id'];

    public function buyer() //購入者（利用者）のリレーション
    {
    	return $this->belongsTo('App\User', 'buyer_id');
    }

	public function seller() //出品者（コンビニ）のリレーション
	{
		return $this->belongsTo('App\User', 'seller_id');
	}

	public function product() //商品のリレーション
	{
		return $this->belongsTo('App\Product');
	}
}
