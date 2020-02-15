<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function getUser($id)
    {
        $user = User::find($id);

        // updated_at > created_at > idで評価する
        $posts = $user->posts->toArray();
        usort($posts, function ($a, $b) {
            if ($a['updated_at'] != $b['updated_at']) {
                return ($a['updated_at'] < $b['updated_at']) ? +1 : -1;
            }

            if ($a['created_at'] != $b['created_at']) {
                return ($a['created_at'] < $b['created_at']) ? +1 : -1;
            }

            return ($a['id'] < $b['id']) ? +1 : -1;
        });

        return response([
            'user' => $user,
            'posts' => $posts,
        ], 200);
    }

}
