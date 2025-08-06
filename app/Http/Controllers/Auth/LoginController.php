<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserTemp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Path to your login view
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard'); // Or wherever you want
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function verifyemailid(Request $request)
    {
        $token = $request->query('token');

        $userTemp = UserTemp::where('verify_token', $token)
            ->where('otp_expires_at', '>', now())
            ->first();

        if (!$userTemp) {
            return redirect()->route('home')->with('error', 'Email verification link expired');
        }
        $user = User::where('email', $userTemp->email)->first();
        if ($user) {
            return redirect()->route('home')->with('error', 'Email already verified');
        }
        // Create real user or mark as verified
        $user = User::create([
            'email' => $userTemp->email,
            'name' => $userTemp->name,
            'phone' => $userTemp->phone,
            'email_verified_at' => now(),
            'password' => $userTemp->password,
            // Add name, password, etc.
        ]);

        // Clean up temp record

        return view('auth.verifyemailid');
    }

    public function showResetForm(Request $request)
    {
        $token = $request->get('token');
        $email = $request->get('email');

        return view('auth.reset_password', compact('token', 'email'));
    }

    public function submitResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->where('reset_token_expires_at', '>', now())
            ->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Invalid or expired token.');
        }

        $user->password = Hash::make($request->password);
        $user->reset_token = null;
        $user->reset_token_expires_at = null;
        $user->save();

        return redirect('/login')->with('success', 'Password has been reset successfully.');
    }
}
