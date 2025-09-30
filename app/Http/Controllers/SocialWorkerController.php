<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SocialWorkerController extends Controller
{
    /**
     * Show the social worker dashboard
     */
    public function showDashboard(): View
    {
        $user = Auth::user();

        // Get some basic stats for the dashboard
        $stats = [
            'total_cases' => 0, // You can implement this later
            'active_cases' => 0, // You can implement this later
            'completed_cases' => 0, // You can implement this later
            'pending_reports' => 0, // You can implement this later
        ];

        $viewData = [
            'meta_title' => 'Dashboard | CAMS',
            'meta_desc' => 'CAMS is a secure Child Abuse Management System designed to record, track, and report abuse cases, enabling admins, social workers, and police to collaborate effectively.',
            'meta_image' => url('logo.png'),
            'user' => $user,
            'stats' => $stats,
        ];

        return view('social-worker.dashboard', $viewData);
    }

    /**
     * Show the profile page
     */
    public function showProfile(): View
    {
        $user = Auth::user();

        $viewData = [
            'meta_title' => 'Profile | CAMS',
            'meta_desc' => 'Manage your profile information and settings.',
            'meta_image' => url('logo.png'),
            'user' => $user,
        ];

        return view('social-worker.profile', $viewData);
    }

    /**
     * Update the profile information
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'employee_id' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $data = $request->only(['name', 'email', 'employee_id', 'department', 'phone']);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        Log::info('Social worker profile updated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'updated_fields' => array_keys($data),
        ]);

        return redirect()->route('social-worker.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        Log::info('Social worker password updated', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return redirect()->route('social-worker.profile')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Remove avatar
     */
    public function removeAvatar(): RedirectResponse
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);

            Log::info('Social worker avatar removed', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return redirect()->route('social-worker.profile')
                ->with('success', 'Avatar removed successfully.');
        }

        return redirect()->route('social-worker.profile')
            ->with('error', 'No avatar to remove.');
    }
}
