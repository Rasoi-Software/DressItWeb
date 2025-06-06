<?php

use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Notification;
use App\Models\User; // Ensure you're importing the User model


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
    function returnErrorWithData($message, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data
        ], 200);
    }
}



