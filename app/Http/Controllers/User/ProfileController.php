<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        //validate data 
        $validated = $request->validate([
            'name' => 'required|min:6',
            'username' => 'required|min:6',
            'bio' => 'max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(Auth::user()->id),
            ]
        ]);

        //update database 
        if ($validated) {
            Auth::user()->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    'bio' => $request->bio,
                    'email' => $request->email
                ]);
        } 
    }
}
