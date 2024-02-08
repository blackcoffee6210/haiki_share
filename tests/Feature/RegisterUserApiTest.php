<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterUserApiTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function should_新しいユーザーを作成して返却する()
	{
		//利用者の場合
		$data = [
			'group'                 => 1,
			'name'                  => 'Haiki Share',
			'email'                 => 'sample@email.com',
			'password'              => 'test1234',
			'password_confirmation' => 'test1234'
		];

		//コンビニユーザーの場合
//		$data = [
//			'group'                 => 2,
//			'name'                  => 'Seven Mart',
//			'branch'                => '渋谷支店',
//			'prefecture_id'         => 14,
//			'address'               => 'Shibuya',
//			'email'                 => 'seven@email.com',
//			'password'              => 'test1234',
//			'password_confirmation' => 'test1234'
//		];

		$response = $this->json('POST', route('register'), $data);

		$user = User::first();
		$this->assertEquals($data['name'], $user->name);

		$response->assertStatus(201)
				 ->assertJson(['name' => $user->name]);
	}

}
