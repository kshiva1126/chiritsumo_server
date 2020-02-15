<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
        if ($following = Following::where($data)->first()) {
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
}
