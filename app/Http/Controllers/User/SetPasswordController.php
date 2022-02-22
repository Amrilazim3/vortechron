<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Rules\VerifyPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetPasswordController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => [
                'required',
                'min:9',
                new VerifyPassword
            ],
            'password_confirmation' => 'required|min:9|same:password'
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return true;
    }
}
