<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterProductApiTest extends TestCase
{
	use RefreshDatabase;

	public function setUp(): void
	{
		parent::setUp();
		$this->user = factory();
	}
}
