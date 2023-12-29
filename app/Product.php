<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    //ソフトデリート
	use SoftDeletes;

	protected $fillable = [
		'user_id', 'name', 'category_id', 'image', 'price',
		'expire', 'delete_flg'
	];

	//リレーション
	public function user() {
		return $this->belongsTo('App\User');
	}
	public function category() {
		return $this->belongsTo('App\Category');
	}
}
