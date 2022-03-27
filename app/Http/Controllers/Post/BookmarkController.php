<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllPostResource;
use App\Models\Bookmark;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Bookmark $bookmark, Request $request)
    {
        $user = $request->user();

        $bookmarks = $bookmark->where('user_id', $user->id)
            ->orderByDesc('bookmarked_at')
            ->paginate(12);

        $posts = [];
        foreach ($bookmarks as $bookmark) {
            array_push($posts, new AllPostResource($bookmark->post));
        }

        return response()->json([
            'posts' => $posts
        ]);
    }

    public function store(Post $post, Request $request)
    {
        Bookmark::create([
            'post_id' => $post->id,
            'user_id' => $request->userId,
            'bookmarked_at' => now()
        ]);

        return true;
    }

    public function destroy(Post $post, User $user)
    {
        Bookmark::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->delete();
    }
}
