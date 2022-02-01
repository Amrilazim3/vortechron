<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SignUpController extends Controller
{
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|confirmed|min:9'
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)  
        ]);

        if ($user) {
            return response(['success' => 'user is created']);
        }
    }
}
