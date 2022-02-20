<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\ValidateNewPassword;
use App\Rules\VerifyPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        $request->validate([
            'email' => 'required|exists:users,email|email',
            'password' => [
                'required',
                'min:9',
                new VerifyPassword,
                new ValidateNewPassword($user)
            ],
            'password_confirmation' => 'required|min:9|same:password',
            'token' => 'required',
        ]);

        Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }  
        );
        
        return true;
    }
}
