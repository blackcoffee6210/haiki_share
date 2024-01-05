<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $fillable = ['name'];

    public function reviews() //リレーション
    {
    	return $this->hasMany('App\Review');
    }
}
