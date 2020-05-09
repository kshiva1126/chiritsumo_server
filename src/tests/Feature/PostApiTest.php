<?php

namespace Tests\Feature;

use App\Http\Requests\PostRequest;
use App\Post;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use LogicException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\InvalidArgumentException;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザ作成
        $this->user = factory(User::class)->create();
    }

    /**
     * カスタムリクエストの最大値/最小値バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerRequired
     */
    public function 必須項目バリデーション($data, $expcted)
    {
        $request = new PostRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * カスタムリクエストの最大値/最小値バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerMax
     */
    public function 最大値・最小値バリデーション($data, $expcted)
    {
        $request = new PostRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * @test
     */
    public function 投稿する()
    {
        // 投稿
        $postData = [
            'title' => 'title',
            'content' => 'content',
            'writer_id' => $this->user->id,
        ];
        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('post'), $postData);
        $post = Post::first();
        $this->assertEquals($postData, [
            'title' => $post->title,
            'content' => $post->content,
            'writer_id' => $post->writer_id,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'title' => $post->title,
                'content' => $post->content,
            ]);

        $this->編集する($post);
    }

    /**
     * @param Post $post
     */
    public function 編集する($post)
    {
        // 編集
        $editData = [
            'post_id' => $post->id,
            'title'  => 'editedTitle',
            'content' => 'editedContent',
        ];
        $response = $this
                        ->actingAs($this->user)
                        ->json('POST', route('edit'), $editData);
        $edit = Post::first();
        $this->assertEquals($editData, [
            'post_id' => $edit->id,
            'title' => $edit->title,
            'content' => $edit->content,
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'title' => $edit->title,
                'content' => $edit->content,
            ]);
    }

    public function providerRequired()
    {
        // 必須項目: title, content
        return [
            // title
            [['title' => null, 'content' => 'content', 'writer_id' => 1], false],
            [['title' => '', 'content' => 'content', 'writer_id' => 1], false],
            [['title' => 'title', 'content' => 'content', 'writer_id' => 1], true],

            // content
            [['title' => 'title', 'content' => null, 'writer_id' => 1], false],
            [['title' => 'title', 'content' => '', 'writer_id' => 1], false],
            [['title' => 'title', 'content' => 'content', 'writer_id' => 1], true],
        ];
    }

    public function providerMax()
    {
        // 最大値 title: 50, content: 1000
        return [
            // title
            [['title' => str_repeat('a', 51), 'content' => 'content', 'writer_id' => 1], false],
            [['title' => str_repeat('a', 50), 'content' => 'content', 'writer_id' => 1], true],

            // content
            [['title' => 'title', 'content' => str_repeat('a', 1001), 'writer_id' => 1], false],
            [['title' => 'title', 'content' => str_repeat('a', 1000), 'writer_id' => 1], true],
        ];
    }
}
