<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('dashboard.settings', compact('user'));
    }

    /**
     * Update profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'avatar' => ['nullable', 'file', 'max:2048'], // 2MB max
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            try {
                $avatar = $request->file('avatar');
                $extension = $avatar->getClientOriginalExtension() ?: 'jpg';
                $filename = 'avatar_' . time() . '_' . uniqid() . '.' . $extension;
                
                // Ensure directory exists
                Storage::disk('public')->makeDirectory('avatars');
                
                // Delete old avatar if exists and is a local file
                if ($user->avatar && str_starts_with($user->avatar, '/storage/avatars/')) {
                    $oldPath = str_replace('/storage/', '', $user->avatar);
                    Storage::disk('public')->delete($oldPath);
                }
                
                // Store the new file
                $stored = $avatar->storeAs('avatars', $filename, 'public');
                
                if ($stored) {
                    $updateData['avatar'] = '/storage/avatars/' . $filename;
                }
            } catch (\Exception $e) {
                \Log::error('Avatar upload failed: ' . $e->getMessage());
            }
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Check if user has a password (OAuth users might not)
        if ($user->password) {
            $request->validate([
                'current_password' => 'required',
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
        } else {
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    /**
     * Delete all user data (scans only)
     */
    public function deleteData()
    {
        $user = Auth::user();
        $user->scans()->delete();

        return back()->with('success', 'All scan data has been deleted.');
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $user = Auth::user();
        
        // Delete all related data
        $user->scans()->delete();
        
        // Logout and delete user
        Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}
