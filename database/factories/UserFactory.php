<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

/**
 * Fakerプロパティの情報
 * https://qiita.com/tosite0345/items/1d47961947a6770053af
 */

$factory->define(User::class, function (Faker $faker) {

	//グループの数値をランダムで変数に入れる
//	$group = mt_rand(1, 2);
	$group = 2;

	//お店の名前を配列に入れる
	$array = array('Family Store', 'Seven Mart', 'mini step', 'Coco Mart',
						'LOWSON', 'SEVEN TWELVE');
	//お店の名前のキーをランダムで取得する
	$key = array_rand($array, 1);

    return [
    	'group'             => $group,
        'name'              => ($group === 2) ? $array[$key] : $faker->name,
	    'branch'            => ($group === 2) ? $faker->city : null,
	    'prefecture_id'     => ($group === 2) ? mt_rand(1, 47) : null,
	    'address'           => ($group === 2) ? $faker->streetAddress : null,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(10),
    ];
});
