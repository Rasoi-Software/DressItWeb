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

        $response = Http::withHeaders([
            'api-key' => env('BREVO_API_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => 'Dress It',
                'email' => 'no-reply@dressitnow.com'
            ],
            'to' => [
                [
                    'email' => $email,
                    'name' => $name
                ]
            ],
            'subject' => 'Your OTP Code',
            'htmlContent' => "<p>Your OTP code is: <strong>{$otp}</strong></p>",
        ]);

        if ($response->successful()) {
            return response()->json(['message' => 'OTP sent successfully!', 'otp' => $otp]);
        } else {
            return response()->json(['message' => 'Failed to send OTP', 'error' => $response->json()], 500);
        }
    }
}
