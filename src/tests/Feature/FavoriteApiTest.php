<?php

namespace Tests\Feature;

use App\Favorite;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FavoriteApiTest extends TestCase
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
    public function お気に入りする()
    {
        $post = $this->投稿する();

        $data = [
            'post_id' => $post->id,
        ];

        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('fav'), $data);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'is_delete_favo' => false,
            ]);
    }

    public function 投稿する()
    {
        $data = [
            'title' => 'title',
            'content' => 'content',
            'writer_id' => $this->user->id,
        ];
        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('post'), $data);
        return Post::first();
    }
}
