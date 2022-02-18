<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetPasswordController extends Controller
{
    public function setPassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => 'required|min:9',
            'password_confirmation' => 'required|min:9|same:password'
        ]);

        $user->update([
            'password' => bcrypt($request->password)
        ]);

        return true;
    }
}
