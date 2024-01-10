<?php

namespace Tests\Feature;

use App\Product;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeApiTest extends TestCase
{
	use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();

		$this->user = factory(User::class)->create();

		factory(Product::class)->create();
		$this->product = Product::first();
	}

	/**
	 * @test
	 */
	public function should_いいねを追加できる()
	{
		$response = $this->actingAs($this->user)
						 ->json('POST', route('product.like', [
						 	'id' => $this->product->id
						 ]));

		$response->assertStatus(200)
				 ->assertJsonFragment([
				 	'product_id' => $this->product->id
				 ]);

		$this->assertEquals(1, $this->product->likes()->count());
	}

	/*
	 * @test
	 */
	public function should_2回同じ商品にいいねしても1個しかいいねがつかない()
	{
		$param = ['id' => $this->product->id];
		$this->actingAs($this->user)->json('POST', route('product.like', $param));
		$this->actingAs($this->user)->json('POST', route('product.like', $param));

		$this->assertEquals(1, $this->product->likes()->count());
	}

	/**
	 * @test
	 */
	public function should_いいねを解除できる()
	{
		$this->product->likes()->attach($this->user->id);

		$response = $this->actingAs($this->user)
						 ->json('DELETE', route('product.like', [
						 	'id' => $this->product->id
						 ]));

		$response->assertStatus(200)
				 ->assertJsonFragment([
				 	'product_id' => $this->product->id
				 ]);

		$this->assertEquals(0, $this->product->likes()->count());
	}
}






























