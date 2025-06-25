<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;

class PasswordOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
       // $request->validate(['email' => 'required|email']);
        $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return returnError('Email not found.');
        }

        $otp = random_int(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        // Send OTP via email
        Mail::raw("Your OTP is: {$otp}", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset OTP');
        });

        return returnSuccess('OTP sent to your email.');
    }

    public function resetWithOtp(Request $request)
    {
        // $request->validate([
        //     'email' => 'required|email',
        //     'otp' => 'required|digits:6',
        //     'password' => 'required|string|min:6|confirmed',
        // ]);
        $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|digits:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return returnErrorWithData('Validation failed', $validator->errors());
            }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return returnError('Invalid email.');
        }

        if (
            !$user->otp ||
            $user->otp !== $request->otp ||
            Carbon::parse($user->otp_expires_at)->isPast()
        ) {
            return returnError('Invalid or expired OTP.');
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return returnSuccess('Password has been reset successfully.');
    }
}
