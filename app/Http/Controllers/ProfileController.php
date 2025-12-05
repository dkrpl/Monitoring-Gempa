<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    use LogsActivity;

    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        $timezones = $this->getTimezones();
        $languages = $this->getLanguages();

        return view('profile.edit', compact('user', 'timezones', 'languages'));
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'timezone' => ['required', 'string'],
            'language' => ['required', 'in:en,id'],
            'bio' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $oldData = $user->toArray();

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'timezone' => $request->timezone,
            'language' => $request->language,
            'bio' => $request->bio,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            $imagePath = $request->file('image')->store('profile-images', 'public');
            $userData['image'] = $imagePath;
        }

        $user->update($userData);

        // Log the activity
        $changes = array_diff_assoc($user->toArray(), $oldData);
        if (!empty($changes)) {
            $this->logProfileUpdate($user, $changes);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $this->logActivity('password_change', 'User changed password', $user);

        return redirect()->route('profile.show')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email_notifications' => ['boolean'],
            'sms_notifications' => ['boolean'],
            'push_notifications' => ['boolean'],
        ]);

        $oldNotifications = [
            'email' => $user->email_notifications,
            'sms' => $user->sms_notifications,
            'push' => $user->push_notifications
        ];

        $user->update([
            'email_notifications' => $request->boolean('email_notifications'),
            'sms_notifications' => $request->boolean('sms_notifications'),
            'push_notifications' => $request->boolean('push_notifications'),
        ]);

        $newNotifications = [
            'email' => $user->email_notifications,
            'sms' => $user->sms_notifications,
            'push' => $user->push_notifications
        ];

        if ($oldNotifications != $newNotifications) {
            $this->logActivity('notification_update', 'User updated notification preferences', $user, [
                'old' => $oldNotifications,
                'new' => $newNotifications
            ]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Notification preferences updated.');
    }

    /**
     * Delete profile image
     */
    public function deleteImage()
    {
        $user = Auth::user();

        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);

            $user->update(['image' => null]);

            $this->logActivity('image_delete', 'User removed profile image', $user);

            return redirect()->route('profile.show')
                ->with('success', 'Profile image removed successfully.');
        }

        return redirect()->route('profile.show')
            ->with('error', 'No profile image found.');
    }

    /**
     * Get available timezones
     */
    private function getTimezones()
    {
        return [
            'Asia/Jakarta' => 'WIB (Jakarta)',
            'Asia/Makassar' => 'WITA (Makassar)',
            'Asia/Jayapura' => 'WIT (Jayapura)',
            'UTC' => 'UTC',
            'Asia/Singapore' => 'Singapore',
            'Asia/Tokyo' => 'Tokyo',
            'America/New_York' => 'New York',
            'Europe/London' => 'London'
        ];
    }

    /**
     * Get available languages
     */
    private function getLanguages()
    {
        return [
            'en' => 'English',
            'id' => 'Indonesian'
        ];
    }
}
