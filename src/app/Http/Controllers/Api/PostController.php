<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Post;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditRequest;
use App\Http\Requests\PostRequest;

class PostController extends Controller
{
    public function post(PostRequest $request)
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

    public function edit(EditRequest $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $data = $request->all();

        $post = Post::where('id', $data['post_id'])->update([
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return response($post, 201);
    }

    public function timeline(Request $request)
    {
        if (!$user = Auth::user()) {
            return response(['auth' => false], 401);
        }

        $timelinePosts = [];
        $followees = $user->followees;
        foreach ($followees as $followee) {
            $timelinePosts[] = $followee->posts;
        }

        return response($timelinePosts, 200);
    }
}
