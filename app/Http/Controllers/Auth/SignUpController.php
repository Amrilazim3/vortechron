<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\VerifyPassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class SignUpController extends Controller
{
    public function signUp(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4|max:50',
            'username' => 'required|min:4|max:15',
            'email' => 'required|unique:users,email|email',
            'password' => [
                'required',
                'min:9',
                'confirmed',
                new VerifyPassword
            ]
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password)  
        ]);

        event(new Registered($user));

        return response(['success' => 'user is created']);
    }
}
