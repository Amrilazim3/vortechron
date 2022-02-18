<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\ValidateNewPassword;
use App\Rules\ValidatePassword;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'old_password' => [
                'required',
                new ValidatePassword($user)
            ],
            'new_password' => [
                'required',
                'min:9',
                new ValidateNewPassword($user)
            ],
            'new_password_confirmation' => [
                'required',
                'same:new_password'
            ]
        ]);

        $user->update([
            'password' => bcrypt($request->new_password) 
        ]);

        return true;
    }

    public function getPassword()
    {
        $user = Auth::user();

        return response()->json([
            'password' => $user->password 
        ]);
    }
}
