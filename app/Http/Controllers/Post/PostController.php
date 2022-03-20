<?php

namespace App\Http\Controllers\Post;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostResource;
use App\Http\Resources\ShowPostResource;

class PostController extends Controller
{
    public function index()
    {
        $resPosts = Post::paginate(20);
        $posts = AllPostResource::collection($resPosts)
                    ->response()
                    ->getData();

        return response()->json([
            'posts' => $posts
        ]);

    }

    public function show(Post $post)
    {
        return response()->json([
            'post' => new ShowPostResource($post)
        ]);
    }
}
