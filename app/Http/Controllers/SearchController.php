<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchUsers()
    {
        $users = User::search(request(['search']))
        ->select('id', 'username', 'image_url')
        ->paginate(5);

        // $posts = Post::search(request(['search']))
        // ->get();

        return response()->json([
            'users' => $users
        ]);
    }
}
