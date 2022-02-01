<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\EmailTakenException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirect($service)
    {
        return [
            'url' => Socialite::driver($service)->stateless()->redirect()->getTargetUrl()
        ];
    }

    public function handleCallback($service)
    {
        $user = Socialite::driver($service)->stateless()->user();
        $user = $this->findOrCreateUser($service, $user);

        return view('oauth/callback', [
            'token' => $user->id,
        ]);
    }

    protected function findOrCreateUser($service, $user)
    {
        $oauthService = User::where('service', $service)
            ->where('service_id',  $user->getId())
            ->first();

        if ($oauthService) {
            return $oauthService;
        }

        if (User::where('email', $user->getEmail())->exists()) {
            throw new EmailTakenException();
        }

        return $this->createUser($service, $user);
    }

    protected function createUser($service, $sUser)
    {
        $user = User::create([
            'name' => $sUser->getName() ? $sUser->getName() : $sUser->getNickname(),
            'username' => $sUser->getNickname() ? $sUser->getNickname() : $sUser->getName(),
            'email' => $sUser->getEmail(),
            'service' => $service,
            'service_id' => $sUser->getId(),
            'password' => bcrypt('XXXXXXXXXX')
        ]);

        return $user;
    }

    public function OAuthSignIn(User $user)
    {
        return [
            'user' => $user
        ];
    }
}
