<?php

namespace App\Http\Controllers\User\Account;


use App\Rules\ValidatePassword;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EditProfileController extends Controller
{
    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'required|min:4|max:50',
            'username' => 'required|min:4|max:15',
            'bio' => 'nullable|max:100',
            'file' => 'nullable|image|file'
        ]);

        $user = Auth::user();

        //update database
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'bio' => $request->bio,
            'image_url' => $request->hasFile('file') ?
                $this->updateFile($request, $user) :
                $user->image_url
        ]);

        return response()->json([
            'user' => $user
        ]);
    }

    public function removeFile()
    {
        $user = Auth::user();

        Storage::disk('public')->delete($user->image_url);

        Auth::user()->update([
            'image_url' => null
        ]);

        return response()->json([
            'user' => $user
        ]);
    }

    protected function updateFile($request, $user)
    {
        Storage::disk('public')->delete($user->image_url);

        return $request->file('file')->store('images', 'public');
    }

    public function changeEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],
            'password' => ['required', new ValidatePassword($user)]
        ]);

        $user->update([
            'email_verified_at' => null,
            'email' => $request->email
        ]);

        event(new Registered($user));

        return true;
    }
}
