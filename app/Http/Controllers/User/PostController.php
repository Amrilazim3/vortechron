<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowPostResource;
use App\Http\Resources\UserPostResource;
use App\Models\Category;
use App\Models\Post;
use App\Traits\ArrayPaginable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use ArrayPaginable;

    public function index(Request $request)
    {
        $user = $request->user();

        $postsCount = $user::withCount('posts')
            ->find($user->id)
            ->posts_count;

        $resPosts = $this->paginate(
            $user->posts,
            12
        );

        $posts = UserPostResource::collection($resPosts)
            ->response()
            ->getData();

        return response()->json([
            'posts' => $posts,
            'posts_count' => $postsCount
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:50',
            'thumbnail' => 'nullable|image|file',
            'excerpt' => 'required|max:100',
            'body' => 'required|min:100',
            'category' => 'nullable'
        ]);

        Post::create([
            'user_id' => $request->user()->id,
            'category_id' => $request->category ? 
                $this->findCategoryId($request->category) : 
                null,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(10) ,
            'thumbnail' => $request->hasFile('thumbnail') ? 
                $this->insertThumbnail($request) :
                null,
            'excerpt' => $request->excerpt,
            'body' => '<p>' . $request->body . '</p>',
        ]);
        
        return response()->json([
            'success' => 'posts has been created'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json([
            'post' => new ShowPostResource($post)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        //
    }

    // addtional function
    public function insertThumbnail($request)
    {
        $thumbnail = $request->file('thumbnail')->store('posts', 'public');
        return asset('storage/' . $thumbnail);
    }

    public function findCategoryId($category) 
    {
        $category = Category::where('name', $category)->first();

        if ($category) {
            return $category->id;
        } else {
            return false;
        }
    }
}
