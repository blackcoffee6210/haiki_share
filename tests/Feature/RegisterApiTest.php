<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterApiTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function should_新しいユーザーを作成して返却する()
	{
		$data = [
			'group'                 => 1,
			'name'                  => 'Haiki',
			'email'                 => 'haiki_share@email.com',
			'password'              => 'password',
			'password_confirmation' => 'password',
		];
//		$data = [
//			'group'                 => 2,
//			'name'                  => 'Haiki Share',
//			'branch'                => '渋谷店',
//			'prefecture_id'         => 1,
//			'address'               => '渋谷区宇田川町26-4',
//			'email'                 => 'haiki_share@email.com',
//			'password'              => 'password',
//			'password_confirmation' => 'password',
//		];

		$response = $this->json('POST', route('register'), $data);
		$user = User::first();
		$this->assertEquals($data['name'], $user->name);

		$response->assertStatus(201) //登録なのでCREATED(201)のステータスになる
				 ->assertJson(['name' => $user->name]);
	}
}
