<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageUploadApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ファイルをアップロードできる()
    {
        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('image.create'), [
                            // ダミーファイルを作成して送信している
                            'image' => UploadedFile::fake()->image('photo.jpg'),
                        ]);

        $response->assertStatus(201);


    }

}
