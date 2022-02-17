<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignInController extends Controller
{
    public function signIn(Request $request)
    {
        $oauthSignIn = $this->oauthSignIn($request);

        if ($oauthSignIn) {
            return true;
        } 

        $credentials = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return true;
        } else {
            return response()->json([
                'error' => 'something went wrong.'
            ], 422);
        }
    }

    protected function oauthSignIn($request)
    {
        $oauthservice = User::where('email', $request->email)
        ->where('password', null)
        ->first();

        if ($oauthservice) {
            Auth::login($oauthservice);
            $request->session()->regenerate();
            return true;
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    }
}
