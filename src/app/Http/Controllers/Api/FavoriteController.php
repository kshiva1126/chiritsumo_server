<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Favorite;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

class FavoriteController extends Controller
{
    public function fav(Request $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $this->validate($request, [
            'user_id' => ['required', 'integer'],
            'post_id' => ['required', 'integer'],
        ]);

        $data = $request->all();

        $delete_favo = false;
        if ($favorite = Favorite::where($data)->first()) {
            if ($favorite instanceof Favorite) {
                $favorite->delete();
                $delete_favo = true;
            }
        } else {
            Favorite::create($data);
        }

        return response([
            'is_delete_favo' => $delete_favo,
        ], 201);
    }
}
