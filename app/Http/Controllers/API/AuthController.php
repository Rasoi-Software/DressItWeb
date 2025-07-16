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
use App\Mail\EmailVerificationMail;
use App\Models\UserTemp;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

            $validated = $validator->validated();

            $otp = random_int(100000, 999999);

            $userTemp = UserTemp::updateOrCreate(
                ['email' => $validated['email']], // Match by email
                [
                    'name' => $validated['name'],
                    'phone' => $request->phone,
                    'password' => bcrypt($validated['password']),
                    'otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ]
            );

            $this->sendOtpToUser($userTemp); // Reusable method for both temp and real users

            return returnSuccess('OTP sent to your email. Please verify your email');
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }


    public function resendOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

            $user = UserTemp::where('email', $request->email)->first();

            if (!$user) {
                $user = User::where('email', $request->email)->first();
            }
            if (!$user) {
                return returnErrorWithData('User not found', [], 404);
            }

            $this->sendOtpToUser($user);

            return returnSuccess('OTP sent to your email. Please verify your email');
        } catch (\Exception $e) {
            return returnError($e->getMessage());
        }
    }

    private function sendOtpToUser($user)
    {
        $otp = random_int(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        $data = [
            'name'       => $user->name,
            'email'      => $user->email,
            'subject'    => 'Verify Your Email Address with OTP',
            'otp'        => $otp,
            'expires_at' => '10 minutes'
        ];

        Mail::to($user->email)->send(new EmailVerificationMail($data));
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', function ($attribute, $value, $fail) {
                $existsInUsers = \App\Models\User::where('email', $value)->exists();
                $existsInTemp = \App\Models\UserTemp::where('email', $value)->exists();

                if (!$existsInUsers && !$existsInTemp) {
                    $fail("The $attribute does not exist in our records.");
                }
            }],
            'otp' => 'required|digits:6'
        ]);


        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $userTemp = UserTemp::where('email', $request->email)->first();

        if (!$userTemp) {
            $userTemp = User::where('email', $request->email)->first();
        }
        if (!$userTemp) {
            return returnError('User not found.');
        }

        if ($userTemp->otp !== $request->otp) {
            return returnError('Invalid OTP.');
        }

        if ($userTemp->otp_expires_at < now()) {
            return returnError('OTP has expired.');
        }

        if($request->is_forgot_password==true){
             return returnSuccess('Email verified successfully.', $userTemp);
        }

        // Move to main users table
        $user = User::create([
            'name' => $userTemp->name,
            'email' => $userTemp->email,
            'phone' => $userTemp->phone,
            'password' => $userTemp->password,
            'email_verified_at' => now()
        ]);

        // Delete temp user
        $userTemp->delete();

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
                return returnErrorWithData('Validation failed', $validator->errors(), 400);
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
