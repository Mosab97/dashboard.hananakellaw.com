<?php

namespace App\Http\Controllers\CP\UserManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\CP\UserProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('CP.user-management.users.user-profile.index', compact('user'));
    }

    public function update(UserProfileRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Check current password if changing password
        if (isset($validated['current_password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => t('Current password does not match')]);
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? $user->mobile,
        ];

        // Update password if provided
        if (isset($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->avatar != 'undefined') {
            Log::info('Processing avatar upload');
            $oldAvatar = $user->avatar;

            $data['avatar'] = uploadImage($request->file('avatar'), 'users');

            if ($oldAvatar) {
                deleteFile($oldAvatar, 'users');
            }

            Log::info('New avatar uploaded', ['path' => $data['avatar']]);
        }

        // Handle avatar removal
        if ($request->has('avatar_remove') && $request->input('avatar_remove') == '1') {
            // Delete old avatar if exists and not the default one
            $oldAvatar = $user->getRawOriginal('avatar');
            if ($oldAvatar && file_exists(public_path($oldAvatar))) {
                @unlink(public_path($oldAvatar));
            }

            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('user.profile')->with('success', t('Profile updated successfully'));
    }
}
