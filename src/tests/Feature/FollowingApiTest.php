<?php

namespace Tests\Feature;

use App\Following;
use App\Http\Requests\FollowingRequest;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class FollowingApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // テストユーザ作成
        $this->followee = factory(User::class)->create();
        $this->follower = factory(User::class)->create();
    }

    /**
     * カスタムリクエストの必須バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerRequired
     */
    public function 必須項目バリデーション($data, $expcted)
    {
        $request = new FollowingRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * @test
     */
    public function フォローする()
    {
        $data = [
            'following_user_id' => $this->followee->id,
        ];

        $response = $this
            ->actingAs($this->follower)
            ->json('POST', route('following'), $data);
        $following = Following::first();
        $this->assertEquals($data, [
            'following_user_id' => $following->following_user_id,
        ]);

        // フォローしたのでfalseが返る
        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'is_defollowing' => false,
            ]);

        $this->フォロイーを取得する();

        $this->フォローしている();

        $this->フォローしていない();
    }

    public function フォロイーを取得する()
    {
        $response = $this
            ->actingAs($this->follower)
            ->json('GET', route('followee', [
                'id' => $this->follower->id,
            ]));

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => $this->followee->name
            ]);
    }

    public function フォローしている()
    {
        $request = $this->json('GET', route('is_following', [
            'id' => $this->followee->id,
        ]));

        $request
            ->assertStatus(200)
            ->assertJsonFragment([
                'is_following' => true,
            ]);
    }

    public function フォローしていない()
    {
        $this->json('POST', route('logout'));

        $request = $this
            ->actingAs($this->followee)
            ->json('GET', route('is_following', [
                'id' => $this->follower->id,
            ]));

        $request
            ->assertStatus(200)
            ->assertJsonFragment([
                'is_following' => false,
            ]);
    }

    public function providerRequired()
    {
        return [
            [['following_user_id' => null], false],
            [['following_user_id' => 2], true],
        ];
    }
}
