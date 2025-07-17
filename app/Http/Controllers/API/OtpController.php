<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    public function sendOtpEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return returnErrorWithData('Validation failed', $validator->errors());
        }

        $email = $request->input('email');
        $name = $request->input('name');
        $otp   = rand(100000, 999999); // generate OTP

        $response = sendOtpEmail($email, $name, $otp);

        if ($response->successful()) {
            return returnSuccess('OTP sent successfully');
        } else {
            return returnError('Failed to send OTP', $response->json());
        }
    }
}
