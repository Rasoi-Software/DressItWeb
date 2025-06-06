<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($message = '', $data = [], $token = null, $statusCode = 200)
    {
        $responeData[] = $data;
        if ($token) {
            $responeData['token'] = $token;
        }
        $response = [
            'status' => true,
            'message' => $message,
            'data' => $data,
        ];

        return response()->json($response, $statusCode);
    }

    public static function error($message = '', $data = [], $statusCode = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $statusCode);

        return ResponseHelper::success(__('messages.login_success'), ['user' => $user], $token);
    }
}
