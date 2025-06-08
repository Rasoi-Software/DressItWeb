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

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

            $validated = $validator->validated();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $request->phone,
                'password' => bcrypt($validated['password']),
            ]);

            $data = [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => $user
            ];

            $otp = rand(100000, 999999);
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Send OTP via email
            Mail::raw("Your OTP is: {$otp}", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Password Reset OTP');
            });

            return returnSuccess('OTP sent to your email.Please verify your email');

            // return returnSuccess('User registered successfully', $data);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function verifyOtp(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|digits:6'

        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return returnError('User not found.');
        }

        if ($user->otp !== $request->otp) {
            return returnError('Invalid OTP.');
        }

        if ($user->otp_expires_at < now()) {
            return returnError('OTP has expired.');
        }

        // Optional: mark email as verified
        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return returnSuccess('Email verified successfully.', [
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user,
        ]);
    }


    public function login(Request $request)
    {
        try {
            // $request->validate([
            //     'email' => 'required|email',
            //     'password' => 'required',
            // ]);
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

            $user = User::where('email', $request->email)->first();

            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages(['email' => ['Login credentials are incorrect.']]);
            }

            $data = [
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => $user
            ];
            return returnSuccess('User login successfully', $data);
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json(['status' => __($status)]);
    }
}
