<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostResource;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function index(Like $like, Request $request)
    {
        $user = $request->user();

        $likes = $like->where('user_id', $user->id)
            ->orderByDesc('liked_at')
            ->paginate(12);

        $posts = [];
        foreach ($likes as $like) {
            array_push($posts, new AllPostResource($like->post));
        }

        return response()->json([
            'posts' => $posts
        ]);
    }

    public function store(Post $post, Request $request)
    {
        Like::create([
            'post_id' => $post->id,
            'user_id' => $request->userId,
            'liked_at' => now()
        ]);

        return true;
    }

    public function destroy(Post $post, User $user)
    {
        Like::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->delete();
    }
}
