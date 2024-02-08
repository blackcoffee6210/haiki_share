<?php

namespace Tests\Feature;

use App\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductListApiTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function should_正しい構造のJSONを返却する()
	{
		factory(Product::class, 5)->create(); //5つの商品データを生成する

		$response = $this->json('GET', route('index'));

		$products = Product::with(['user'])->orderByDesc('created_at')->get();

		$expected_data = $products->map(function ($product) {
			return [
				'id' => $product->id,
				'user' => [
					'name' => $product->user->name
				],
			];
		})
		->all();

		$response->assertStatus(200)
				 ->assertJsonCount(5, 'data')
				 ->assertJsonFragment([
				 	"data" => $expected_data
				 ]);
	}
}
