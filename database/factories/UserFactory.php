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

    return [
    	'group'             => 2,
        'name'              => $faker->name,
	    'branch'            => $faker->text(10),
	    'prefecture_id'     => mt_rand(1, 47),
	    'address'           => $faker->text(20),
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(10),
    ];
});
