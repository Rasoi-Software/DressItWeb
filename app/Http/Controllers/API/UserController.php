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
        if ($request->filled('dob')) $user->dob = $request->dob;
        if ($request->filled('email')) $user->email = $request->email;
        if ($request->filled('phone')) $user->phone = $request->phone;
        if ($request->filled('location')) $user->location = $request->location;
        if ($request->filled('age')) $user->age = $request->age;
        if ($request->filled('bio')) $user->bio = $request->bio;
        if ($request->filled('password')) $user->password = bcrypt($request->password);

        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $imagePath;
        }

        $user->save();

        return returnSuccess('Profile updated successfully', $user);
    }
}
