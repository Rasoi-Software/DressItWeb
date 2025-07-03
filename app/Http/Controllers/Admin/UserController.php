<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'password'        => 'required|min:6|confirmed',
            'phone'           => 'nullable|string|max:20',
            'nickname'        => 'nullable|string|max:255',
            'gender'          => 'nullable|in:male,female,other',
            'interested_in'   => 'nullable|in:male,female,other',
            'location'        => 'nullable|string|max:255',
            'dob'             => 'nullable|date',
            'bio'             => 'nullable|string',
            'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->except(['password', 'profile_image', 'dob']);
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'user';

        if ($request->filled('dob')) {
            $data['dob'] = $request->dob;
            $data['age'] = Carbon::parse($request->dob)->age;
        }

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email,' . $user->id,
            'password'        => 'nullable|min:6|confirmed',
            'phone'           => 'nullable|string|max:20',
            'nickname'        => 'nullable|string|max:255',
            'gender'          => 'nullable|in:male,female,other',
            'interested_in'   => 'nullable|in:male,female,other',
            'location'        => 'nullable|string|max:255',
            'dob'             => 'nullable|date',
            'bio'             => 'nullable|string',
            'profile_image'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'role'            => 'nullable|in:user,admin',
        ]);

        $data = $request->except(['password', 'profile_image', 'dob']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->filled('dob')) {
            $data['dob'] = $request->dob;
            $data['age'] = Carbon::parse($request->dob)->age;
        }

        if ($request->hasFile('profile_image')) {
            // delete old image
            if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('profile_images', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->profile_image && Storage::disk('public')->exists($user->profile_image)) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
