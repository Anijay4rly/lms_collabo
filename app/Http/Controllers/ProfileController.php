<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function settings()
    {
        return view('profile.settings');
    }

    public function index()
    {
        return view('profile.index');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'fname'            => 'required|string|max:255',
            'mname'            => 'nullable|string|max:255',
            'lname'            => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_image'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'signature_file'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature_draw'   => 'nullable|string',
            'password'         => 'nullable|string|min:6|confirmed',
        ]);

        $user->fname = $request->fname;
        $user->mname = $request->mname;
        $user->lname = $request->lname;
        $user->email = $request->email;

        /*
        |--------------------------------------------------------------------------
        | Profile Image Upload
        |--------------------------------------------------------------------------
        */
        if ($request->hasFile('profile_image')) {

            // Delete old image
            if ($user->profile_image && Storage::disk('public')->exists(str_replace('/storage/', '', $user->profile_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->profile_image));
            }

            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = '/storage/' . $path;
        }

        /*
        |--------------------------------------------------------------------------
        | Signature Handling
        |--------------------------------------------------------------------------
        */

        // Case 1: Uploaded signature file
        if ($request->hasFile('signature_file')) {

            // Delete old signature
            if ($user->signature && Storage::disk('public')->exists(str_replace('/storage/', '', $user->signature))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->signature));
            }

            $path = $request->file('signature_file')->store('signatures', 'public');
            $user->signature = '/storage/' . $path;
        }

        // Case 2: Drawn signature (Base64)
        elseif ($request->filled('signature_draw')) {

            // Delete old signature
            if ($user->signature && Storage::disk('public')->exists(str_replace('/storage/', '', $user->signature))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->signature));
            }

            $image = $request->signature_draw;

            // Remove base64 header
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);

            $imageName = 'signature_' . $user->id . '_' . time() . '.png';
            $filePath  = 'signatures/' . $imageName;

            Storage::disk('public')->put($filePath, base64_decode($image));

            $user->signature = '/storage/' . $filePath;
        }

        /*
        |--------------------------------------------------------------------------
        | Password Update
        |--------------------------------------------------------------------------
        */
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()
            ->route('profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function edit()
    {
        return view('profile.edit');
    }
}
