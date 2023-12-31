<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prefecture extends Model
{
    protected $fillable = ['name', 'kana'];

    //リレーション
	public function users()
	{
		$this->hasMany('App\User');
	}
}
