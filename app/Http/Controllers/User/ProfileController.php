<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'required|min:6',
            'username' => 'required|min:6',
            'bio' => 'max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(Auth::user()->id),
            ],
            'file' => 'nullable|image|file'
        ]);

        $user = Auth::user();
        
        //update database
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'bio' => $request->bio,
            'email' => $request->email,
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
}
