<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

	public function products() //リレーション
	{
		return $this->hasMany('App\Product');
	}
}
