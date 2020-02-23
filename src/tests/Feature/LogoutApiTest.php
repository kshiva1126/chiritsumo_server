<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザ作成
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_認証済みのユーザをログアウトさせる()
    {
        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('logout'));

        $response->assertStatus(200);
        $this->assertGuest();
    }
}
