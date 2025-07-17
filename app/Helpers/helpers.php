<?php

use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User; // Ensure you're importing the User model
use Illuminate\Support\Facades\Http;

if (!function_exists('returnSuccess')) {
    function returnSuccess($message, $data = null)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], 200);
    }
}
if (!function_exists('returnError')) {
    function returnError($message)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], 200);
    }
}
if (!function_exists('returnErrorWithData')) {
    function returnErrorWithData($message, $data = null,$custom_code=null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'custom_code' => $message,
            'data' => $data
        ], 200);
    }
}






 function sendOtpEmail($email, $name, $otp)
    {
        return Http::withHeaders([
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
            'templateId' => 1, // Replace with your template ID
            'params' => [
                'code' => $otp
            ]
        ]);
    }