<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditRequest;
use App\Http\Requests\PostRequest;
use App\User;

class PostController extends Controller
{
    public function post(PostRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $data = $request->all();

        $post = Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'writer_id' => $user->id,
        ]);

        return response($post, 201);
    }

    public function edit(EditRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $data = $request->all();

        Post::where('id', $data['post_id'])->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        $post = Post::find($data['post_id']);

        return response($post, 201);
    }

    public function getPost($id)
    {
        $post = Post::find($id);
        $user = User::find($post->writer_id);
        return response(array_merge($post->toArray(), [
            'writer_name' => $user['name'],
        ]), 200);
    }
}
