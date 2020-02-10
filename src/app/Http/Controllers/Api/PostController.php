<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Post;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function post(Request $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $this->validate($request, [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $data = $request->all();

        $post = Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'writer_id' => $user->id,
        ]);

        return response($post, 201);
    }

    public function edit(Request $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $this->validate($request, [
            'post_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
        ]);

        $data = $request->all();

        $post = Post::where('id', $data['post_id'])->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return response($post, 201);
    }
}
