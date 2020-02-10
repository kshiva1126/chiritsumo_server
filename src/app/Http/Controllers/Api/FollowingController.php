<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Following;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class FollowingController extends Controller
{
    public function following(Request $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $this->validate($request, [
            'user_id' => ['required', 'integer'],
            'following_user_id' => ['required', 'integer'],
        ]);

        $data = $request->all();

        $defollowing= false;
        if ($following = Following::where($data)->first()) {
            if ($following instanceof Following) {
                $following->delete();
                $defollowing = true;
            }
        } else {
            Following::create($data);
        }

        return response([
            'is_defollowing' => $defollowing,
        ], 201);
    }
}
