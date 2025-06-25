<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PasswordOtpController;
use App\Http\Controllers\API\LookController;
use App\Http\Controllers\API\LookCommentController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\FollowController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetWithOtp']);




Route::middleware('auth:sanctum')->group(function () {

    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/my-profile', [UserController::class, 'getMyProfile']);
    Route::get('/user/profile/{id}', [UserController::class, 'getProfile']);

    Route::get('/search/looks', [LookController::class, 'search_look']);
    Route::get('/all-looks', [LookController::class, 'all_looks']);
    Route::get('/all-looks/{id}', [LookController::class, 'all_looks']);
    Route::get('/looks', [LookController::class, 'index']);
    Route::post('/looks', [LookController::class, 'store']);
    Route::get('/looks/{id}', [LookController::class, 'show']);
    Route::put('/looks/{id}', [LookController::class, 'update']);
    Route::delete('/looks/{id}', [LookController::class, 'destroy']);

     Route::put('/looks-like-unlike/{look}', [LookCommentController::class, 'toggleLike']);

    Route::get('/looks-comments/{look}', [LookCommentController::class, 'index']);
    Route::post('/looks-comments/{look}', [LookCommentController::class, 'store']);
    Route::get('/comments/{id}', [LookCommentController::class, 'show']);
    Route::put('/comments/{id}', [LookCommentController::class, 'update']);
    Route::delete('/comments/{id}', [LookCommentController::class, 'destroy']);

    Route::post('/send-message', [ChatController::class, 'sendMessage']);
    Route::post('/chat/upload-media', [ChatController::class, 'uploadMedia']);
    Route::get('/chat-history/{userId}', [ChatController::class, 'chatHistory']);


    Route::post('/follow', [FollowController::class, 'follow']);
    Route::post('/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/user/{id}/followers', [FollowController::class, 'followers']);
    Route::get('/user/{id}/following', [FollowController::class, 'following']);
});
