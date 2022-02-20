<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'exists:users,email|required'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['status' => __($status)])
                    : response()->json(['email' => __($status)]);
    }

    public function handle($token, Request $request)
    {
        $email = $request->query('email');
        return redirect(config('app.spa_url') . '/user/account/reset-password?token=' . $token . '&email=' . $email);
    }
}
