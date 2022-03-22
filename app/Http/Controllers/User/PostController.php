<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShowPostResource;
use App\Http\Resources\UserPostResource;
use App\Models\Category;
use App\Models\Post;
use App\Traits\ArrayPaginable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            $user->posts->sortByDesc('created_at'),
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
            'slug' => Str::slug($request->title) . '-' . Str::random(10),
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

    public function show(Post $post)
    {
        return response()->json([
            'post' => new ShowPostResource($post)
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|max:50',
            'thumbnail' => [
                'nullable',
                is_string($request->thumbnail) ? '' : ['image', 'file']
            ],
            'excerpt' => 'required|max:100',
            'body' => 'required|min:100',
            'category' => 'nullable'
        ]);

        $post->update([
            'category_id' => $request->category ? 
                $this->findCategoryId($request->category) : 
                null,
            'title' => $request->title,
            'slug' => Str::slug($request->title) . '-' . Str::random(10),
            'thumbnail' => $request->hasFile('thumbnail') ? 
                $this->updateThumbnail($request, $post->thumbnail) : 
                ($request->thumbnail == '' ? $this->clearThumbnail($post->thumbnail) : $post->thumbnail),
            'excerpt' => $request->excerpt,
            'body' => '<p>' . $request->body . '</p>'
        ]);

        return response()->json([
            'success' => 'posts has been updated'
        ]);
    }

    public function destroy(Post $post)
    {
        if ($post->thumbnail) {
            $this->removeRecentThumbnail($post->thumbnail);
        }

        Post::find($post->id)->delete();

        return true;
    }

    // addtional functions
    protected function insertThumbnail($request)
    {
        $thumbnail = $request->file('thumbnail')->store('posts', 'public');
        return asset('storage/' . $thumbnail);
    }

    protected function findCategoryId($category) 
    {
        $category = Category::where('name', $category)->first();

        if ($category) {
            return $category->id;
        } else {
            return false;
        }
    }

    protected function updateThumbnail($request, $recentThumbnail)
    {
        $this->removeRecentThumbnail($recentThumbnail);

        $newThumbnail = $request->file('thumbnail')->store('posts', 'public');
        return asset('storage/' . $newThumbnail);
    }

    protected function clearThumbnail($recentThumbnail)
    {
        $this->removeRecentThumbnail($recentThumbnail);
        return null;
    }

    protected function removeRecentThumbnail($recentThumbnail)
    {
        $appPath = asset('/storage/');
        $thumbnailPath = str_replace($appPath . '/', "", $recentThumbnail);
        Storage::disk('public')->delete($thumbnailPath);
    }
}
