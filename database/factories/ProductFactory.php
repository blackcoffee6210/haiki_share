<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Category;
use App\Model;
use App\Product;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(Product::class, function (Faker $faker) {

	$file          = UploadedFile::fake()->image('product.jpg');
	$path          = $file->store('public/images');
	$read_tmp_path = str_replace('public/images/','/storage/images/',$path);

	return [
		'user_id'     => function() { return factory(User::class); },
		'image'       => $read_tmp_path,
		'category_id' => mt_rand(1, 11),
		'name'        => $faker->name,
		'detail'      => $faker->text(30),
		'expire'      => date('Y-m-d', strtotime('+'. mt_rand(1, 30). 'day')),
		'price'       => mt_rand(50, 1000)
	];
});
