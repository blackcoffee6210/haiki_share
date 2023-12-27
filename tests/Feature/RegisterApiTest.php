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
			'name'                  => 'Haiki Share',
			'email'                 => 'haiki_share@email.com',
			'password'              => 'password',
			'password_confirmation' => 'password',
		];

		$response = $this->json('POST', route('register'), $data);
		$user = User::first();
		$this->assertEquals($data['name'], $user->name);

		$response->assertStatus(201)
						 ->assertJson(['name' => $user->name]);
	}
}
