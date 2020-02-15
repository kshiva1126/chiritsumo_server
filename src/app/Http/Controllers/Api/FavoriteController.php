<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Favorite;
use App\Http\Controllers\Controller;
use App\Http\Requests\FavoriteRequest;

use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function fav(FavoriteRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $data = $request->all();

        $delete_favo = false;
        if ($favorite = Favorite::where($data)->first()) {
            if ($favorite instanceof Favorite) {
                $favorite->delete();
                $delete_favo = true;
            }
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'post_id' => $data['post_id'],
            ]);
        }

        return response([
            'is_delete_favo' => $delete_favo,
        ], 201);
    }
}
