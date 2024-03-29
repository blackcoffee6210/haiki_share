<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginApiTest extends TestCase
{
	use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = factory(User::class)->create(); //テストユーザー作成
	}

	/**
	 * @test
	 */
	public function should_登録済みのユーザーを認証して返却する()
	{
		$response = $this->json('POST', route('login'), [
			'group'    => 2,
			'email'    => $this->user->email,
			'password' => 'password',
		]);

		$response->assertStatus(200)
				 ->assertJson(['name' => $this->user->name]);

		$this->assertAuthenticatedAs($this->user);
	}
}
