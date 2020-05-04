<?php

namespace Tests\Feature;

use App\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class RegisterApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カスタムリクエストの必須バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerRequired
     */
    public function 必須項目バリデーション($data, $expcted)
    {
        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * カスタムリクエストの最大値/最小値バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerMaxMin
     */
    public function 最大値・最小値バリデーション($data, $expcted)
    {
        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * カスタムリクエストの形式バリデーションテスト
     * @test
     * @param array $data 値
     * @param boolean $expcted 期待値(true:バリデーションOK、false:バリデーションNG)
     * @dataProvider providerFormat
     */
    public function 形式バリデーション($data, $expcted)
    {
        $request = new RegisterRequest();
        $validator = Validator::make($data, $request->rules());
        // 期待値と結果を比較
        $this->assertEquals($expcted, $validator->passes());
    }

    /**
     * @test
     */
    public function 新しいユーザーを作成して返却する()
    {
        $data = [
            'name' => 'vuesplash user',
            'email' => 'dummy@email.com',
            'password' => 'test1234',
            'description' => 'testtest',
            'image' => $this->createTestImageFile(),
        ];

        $response = $this->json('POST', route('register'), $data);
        $user = User::first();

        $this->assertEquals($data['name'], $user->name);

        $response
        ->assertStatus(201)
        ->assertJson([
            'name' => $user->name,
            'image_path' => $user->image_path,
        ]);
    }

    public function providerRequired()
    {
        // 必須項目: name, email, password
        // 任意項目: description, image
        return [
            // name
            [['name' => null, 'email' => 'dummy@email.com', 'password' => 'test1234'], false],
            [['name' => '', 'email' => 'dummy@email.com', 'password' => 'test1234'], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234'], true],
            // email
            [['name' => 'vuesplash user', 'email' => null, 'password' => 'test1234'], false],
            [['name' => 'vuesplash user', 'email' => '', 'password' => 'test1234'], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234'], true],
            // password
            [['name' => 'vuesplash user', 'email' => null, 'password' => null], false],
            [['name' => 'vuesplash user', 'email' => '', 'password' => ''], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234'], true],
        ];
    }

    public function providerMaxMin()
    {
        // 最大値 name: 255, email: 255, password: 100, description: 255, image: 3000
        // 最小値 password: 8
        $file = $this->createTestImageFile();
        return [
            // max
            // name
            [['name' => str_repeat('a', 256), 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $file], false],
            [['name' => str_repeat('a', 255), 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $file], true],

            // email
            [['name' => 'vuesplash user', 'email' => str_repeat('a', 246) . '@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $file], false],
            [['name' => 'vuesplash user', 'email' => str_repeat('a', 245) . '@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $file], true],

            // password
            [['name' => 'vuesplash user', 'email' => '', 'password' => str_repeat('a', 101), 'description' => 'testtest', 'image' => $file], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => str_repeat('a', 100), 'description' => 'testtest', 'image' => $file], true],

            // description
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => str_repeat('a', 256), 'image' => $file], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => str_repeat('a', 255), 'image' => $file], true],

            // image
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $this->createTestImageFile(3001)], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'description' => 'testtest', 'image' => $this->createTestImageFile(3000)], true],

            // min
            // password
            [['name' => 'vuesplash user', 'email' => '', 'password' => str_repeat('a', 7), 'description' => 'testtest', 'image' => $file], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => str_repeat('a', 8), 'description' => 'testtest', 'image' => $file], true],
        ];
    }

    public function providerFormat()
    {
        // email, image: OK(jpg、png、bmp、gif、svg、webp)
        return [
            // email
            [['name' => 'vuesplash user', 'email' => 'dummy', 'password' => 'test1234'], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234'], true],

            // image
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'image' => $this->createTestTxtFile()], false],
            [['name' => 'vuesplash user', 'email' => 'dummy@email.com', 'password' => 'test1234', 'image' => $this->createTestImageFile()], true],
        ];
    }

    public function createTestImageFile(int $file_size = 1000)
    {
        return UploadedFile::fake()->image('test_avatar.png', 200, 100)->size($file_size);
    }

    public function createTestTxtFile(int $file_size = 1000)
    {
        return UploadedFile::fake()->create('hoge.txt', $file_size, 'text/plain');
    }
}
