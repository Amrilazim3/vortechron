<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Traits\ArrayPaginable;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ArrayPaginable;

    public function index(Post $post)
    {
        $resComments = $this->paginate(
            $post->comments->sortByDesc('created_at'),
            20
        );

        $comments = UserCommentResource::collection($resComments)
            ->response()
            ->getData();

        return response()->json([
            'comments' => $comments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required|max:500'
        ]);

        Comment::create([
            'post_id' => $request->postId,
            'user_id' => $request->userId,
            'body' => $request->comment
        ]);

        return true;
    }

    public function destroy(Comment $comment)
    {
        Comment::find($comment->id)->delete();

        return true;
    }
}
