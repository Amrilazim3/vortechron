<?php

namespace App\Http\Controllers\Post;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShowPostResource;
use App\Models\User;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'posts' => $user->posts
        ]);
    }

    public function show(Post $post)
    {
        return response()->json([
            'post' => new ShowPostResource($post)
        ]);
    }
}
