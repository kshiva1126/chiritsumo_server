<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Following;
use App\Http\Controllers\Controller;
use App\Http\Requests\FollowingRequest;

use Illuminate\Support\Facades\DB;

class FollowingController extends Controller
{
    public function following(FollowingRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $data = $request->all();

        $defollowing= false;
        if ($following = Following::where([
            'user_id' => $user->id,
            'following_user_id' => $data['following_user_id'],
        ])->first()) {
            if ($following instanceof Following) {
                $following->delete();
                $defollowing = true;
            }
        } else {
            Following::create([
                'user_id' => $user->id,
                'following_user_id' => $data['following_user_id'],
            ]);
        }

        return response([
            'is_defollowing' => $defollowing,
        ], 201);
    }

    public function getFollowees($id)
    {
        if (!$user = User::find($id)) {
            return response([], 403);
        }

        $isFollowed = $this->isFollowed();
        $followees = $user->followees->toArray();
        return response(array_map(function ($followee) use ($isFollowed) {
            $result = is_array($isFollowed) && in_array($followee['id'], $isFollowed) ? true : false;
            return [
                'id' => $followee['id'],
                'name' => $followee['name'],
                'image_path' => $followee['image_path'],
                'description' => $followee['description'],
                'is_followed' => $result,
            ];
        }, $followees), 200);
    }

    public function getFollowers($id)
    {
        if (!$user = User::find($id)) {
            return response([], 403);
        }

        $isFollowed = $this->isFollowed();
        $followers = $user->followers->toArray();
        return response(array_map(function ($follower) use ($isFollowed) {
            $result = is_array($isFollowed) && in_array($follower['id'], $isFollowed) ? true : false;
            return [
                'id' => $follower['id'],
                'name' => $follower['name'],
                'image_path' => $follower['image_path'],
                'description' => $follower['description'],
                'is_followed' => $result,
            ];
        }, $followers), 200);
    }

    private function isFollowed()
    {
        $isFollowed = false;
        $loginUser = Auth::user();
        if (!$loginUser) {
            return $isFollowed;
        }

        $loginUserFollowers = $loginUser->followers;
        foreach ($loginUserFollowers as $loginUserFollower) {
            if (!$loginUserFollower instanceof User) {
                return $isFollowed;
            }
        }

        return array_map(function ($loginUserFollower) {
            return $loginUserFollower['id'];
        }, $loginUserFollowers->toArray());
    }

    public function isFollowing($following_user_id)
    {
        if (!$user = Auth::user()) {
            return response([], 403);
        }
        $isFollowing = false;
        $where = [
            'user_id' => $user->id,
            'following_user_id' => $following_user_id,
        ];

        if ($following = Following::where($where)->first()) {
            if ($following instanceof Following) {
                $isFollowing = true;
            }
        }

        return response([
            'is_following' => $isFollowing,
        ], 200);
    }
}
