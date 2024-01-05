<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $fillable = ['name', 'kana'];

	public function users() //リレーション
	{
		$this->hasMany('App\User');
	}
}
