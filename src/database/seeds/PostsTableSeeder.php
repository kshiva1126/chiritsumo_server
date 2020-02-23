<?php

use Faker\Factory as Faker;
use App\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('posts')->truncate();
        $faker = Faker::create('en_US');

        for ($h = 1; $h < 4; $h++) {
            for ($i = 0; $i < 5; $i++) {
                $created_at = $faker->dateTime;
                $updated_at = $faker->dateTime;
                while ($created_at > $updated_at) {
                    $updated_at = $faker->dateTime;
                }
                Post::create([
                    "title" => $faker->text(20),
                    "content" => $faker->text(100),
                    "writer_id" => $h,
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                ]);
            }
        }
    }
}
