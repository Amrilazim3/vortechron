<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostResource;
use App\Models\Category;
use App\Models\Post;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all(['name', 'slug']);

        return response()->json([
            'categories' => $categories
        ]);
    }

    public function show(Category $category)
    {
        $resPosts = Post::where('category_id', $category->id)->orderByDesc('created_at')->paginate(20);
        $posts = AllPostResource::collection($resPosts)
                    ->response()
                    ->getData();

        return response()->json([
            'posts' => $posts
        ]);
    }
}
