<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'sometimes|nullable|string|max:20',
            'password' => 'sometimes|nullable|string|min:6|confirmed',
            'location' => 'sometimes|nullable|string|max:255',
            'age' => 'sometimes|nullable|integer|min:1|max:120',
            'bio' => 'sometimes|nullable|string',
            'profile_image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        if ($request->filled('name')) $user->name = $request->name;
        if ($request->filled('nickname')) $user->nickname = $request->nickname;
        if ($request->filled('gender')) $user->gender = $request->gender;
        if ($request->filled('interested_in')) $user->interested_in = $request->interested_in;
        if ($request->filled('email')) $user->email = $request->email;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('location')) $user->location = $request->location;
        if ($request->filled('bio')) $user->bio = $request->bio;
        if ($request->filled('password')) $user->password = bcrypt($request->password);
        if ($request->filled('dob')) {
            $user->dob = $request->dob;

            // Calculate age from DOB
            try {
                $dob = \Carbon\Carbon::parse($request->dob);
                $user->age = $dob->age;
            } catch (\Exception $e) {
                return returnError('Invalid date of birth format. Use YYYY-MM-DD.');
            }
        }
        // if ($request->hasFile('profile_image')) {
        //     $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        //     $user->profile_image = $imagePath;
        // }

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');

            // Force get extension from original name
            $originalName = $image->getClientOriginalName();
            $extension = pathinfo($originalName, PATHINFO_EXTENSION); // "jpg"

            // Sanitize and generate full filename with extension
            $fileName = uniqid() . '_' . pathinfo($originalName, PATHINFO_FILENAME);
            $fileName = preg_replace('/[^a-zA-Z0-9\-_]/', '', $fileName); // clean name
            $fileName = $fileName . '.' . $extension;

            // Upload to S3
            $imagePath = $image->storeAs('profile_images', $fileName, 's3');

            if (!$imagePath || !Storage::disk('s3')->exists($imagePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed or file not found on S3',
                    'path' => $imagePath,
                ]);
            }

            Storage::disk('s3')->setVisibility($imagePath, 'public');

            // Generate public URL
            $imageUrl = Storage::disk('s3')->url($imagePath);
            //dd($imageUrl);
            $user->profile_image = $imageUrl;
        }



        $user->save();

        return returnSuccess('Profile updated successfully', $user);
    }
    public function getMyProfile()
    {
        $user = auth()->user();

        if (!$user) {
            return returnError('User not authenticated');
        }

        return returnSuccess('User profile fetched successfully', $user);
    }
    public function getProfile($id)
    {
        $user = User::find($id);

        if (!$user) {
            return returnError('User not authenticated');
        }

        return returnSuccess('User profile fetched successfully', $user);
    }
    public function alluser()
    {
        $user = User::get();

        return returnSuccess('User profile fetched successfully', $user);
    }

    public function toggleBlock($id)
    {
        try {
            $blocker = auth()->user();
            $blocked = User::findOrFail($id);

            if ($blocker->id == $blocked->id) {
                return returnError("You can't block yourself");
            }

            $isBlocked = $blocker->blockedUsers()->where('blocked_id', $blocked->id)->exists();

            if ($isBlocked) {
                // Unblock
                $blocker->blockedUsers()->detach($blocked->id);
                $message = 'User unblocked successfully';
            } else {
                // Block
                $blocker->blockedUsers()->attach($blocked->id);
                $message = 'User blocked successfully';
            }

            // Get updated list of blocked users
            $blockedUsers = $blocker->blockedUsers()
                ->select('users.id', 'users.name', 'users.email', 'users.profile_image')
                ->get();

            return returnSuccess($message, [
                'blocked_user_id' => $blocked->id,
                'blocked_users'   => $blockedUsers,
            ]);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }
}
