<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ChangeRequest;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use AuthenticatesUsers;

    public function getUser($id)
    {
        $user = User::find($id);

        // updated_at > created_at > idで評価する
        $posts = array_map(function ($post) {
            $writer = User::find($post['writer_id']);
            $short_content = mb_strimwidth($post['content'], 0, 50, '...', 'UTF-8');
            return array_merge($post, [
                'writer_name' => $writer['name'],
                'short_content' => $short_content,
            ]);
        }, $user->posts->toArray());
        $posts = $this->sortPosts($posts);

        return response([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'image_path' => $user->image_path,
                'description' => $user->description,
                'followee' => count($user->followees),
                'follower' => count($user->followers),
            ],
            'posts' => array_merge($posts),
        ], 200);
    }

    public function getTimeline()
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }
        $timelinePosts = [];
        $timelinePosts = array_merge($timelinePosts, $user->posts->toArray());
        foreach ($user->followees as $followee) {
            $timelinePosts = array_merge($timelinePosts, $followee->posts->toArray());
        }
        $timelinePosts = array_map(function ($post) {
            $writer = User::find($post['writer_id']);
            $short_content = mb_strimwidth($post['content'], 0, 50, '...', 'UTF-8');
            return array_merge($post, [
                'writer_name' => $writer['name'],
                'short_content' => $short_content,
            ]);
        }, $timelinePosts);
        $timelinePosts = $this->sortPosts($timelinePosts);
        return response([
            'posts' => $timelinePosts,
        ], 200);
    }

    public function getFavoritePosts($id)
    {
        if (!$user = User::find($id)) {
            return response([], 401);
        }

        $favorite_posts = array_map(function ($post) {
            $writer = User::find($post['writer_id']);
            $short_content = mb_strimwidth($post['content'], 0, 50, '...', 'UTF-8');
            return array_merge($post, [
                'writer_name' => $writer['name'],
                'short_content' => $short_content,
            ]);
        }, $user->favorite_posts->toArray());

        return response([
            'posts' => $favorite_posts,
        ], 200);
    }

    private function sortPosts($posts)
    {
        usort($posts, function ($a, $b) {
            if ($a['updated_at'] != $b['updated_at']) {
                return ($a['updated_at'] < $b['updated_at']) ? +1 : -1;
            }

            if ($a['created_at'] != $b['created_at']) {
                return ($a['created_at'] < $b['created_at']) ? +1 : -1;
            }

            return ($a['id'] < $b['id']) ? +1 : -1;
        });

        return $posts;
    }

    public function checkAuth()
    {
        $userData = [];
        $user = Auth::user();
        $auth = false;
        if ($user) {
            $auth = true;
            $userData = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'description' => $user->description,
                'image_path' => $user->image_path,
            ];
        }

        return response([
            'auth' => $auth,
            'user' => $userData,
        ], 200);
    }

    public function profile(ProfileRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response(['auth' => false], 403);
        }

        $data = $request->all();
        $a = User::where([
            'email' => $data['email']
        ])->first();

        if ($a instanceof User && $user->email !== $a->email) {
            return response([
                'errors' => [
                    'email' => ['このemailは使用されています。'],
                ]
            ], 422);
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'description' => $data['description'],
        ]);

        $this->upload($request, $user);

        return response([], 201);
    }

    public function upload(Request $request, User $user)
    {
        $rules = [
            'file',
            'image',
            'mimes:jpeg, png',
            'dimensions:min_width=120,min_height=120,max_width=400,max_height=400',
        ];
        $this->validate($request, $rules);
        if ($request->hasFile('image') && $request->file('image')->isValid([])) {
            $filename = $request->file('image')->store('public/images');
            $user->image_path = basename($filename);
            $user->save();
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 403);
        }

        $data = $request->all();
        if (!Hash::check($data['old'], $user->password)) {
            return response([
                'errors' => [
                    'old' => ['正しいパスワードを入力してください'],
                ]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($data['new']),
        ]);

        return response([], 201);
    }

    public function getUsersForSearch()
    {
        return response(array_map(function ($user) {
            return [
                $user['id'],
                $user['name'],
            ];
        }, User::all()->toArray()), 200);
    }
}
