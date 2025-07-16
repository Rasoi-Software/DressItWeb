<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PasswordOtpController;
use App\Http\Controllers\API\LookController;
use App\Http\Controllers\API\LookCommentController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\FollowController;
use App\Http\Controllers\API\StripeController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password', [PasswordOtpController::class, 'resetWithOtp']);
Route::post('/stripe/webhook', [StripeController::class, 'handleWebhook']);


Route::post('/looks/draft', [LookController::class, 'storeWithoutLogin']);



Route::middleware('auth:sanctum')->group(function () {

    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/my-profile', [UserController::class, 'getMyProfile']);
    Route::get('/user/profile/{id}', [UserController::class, 'getProfile']);
    Route::get('/alluser', [UserController::class, 'alluser']);

    Route::get('/search/looks', [LookController::class, 'search_look']);
    Route::get('/all-looks', [LookController::class, 'all_looks']);
    Route::get('/all-looks/{id}', [LookController::class, 'all_looks']);
    Route::get('/looks', [LookController::class, 'index']);
    Route::post('/looks', [LookController::class, 'store']);
    Route::get('/looks/{id}', [LookController::class, 'show']);
    Route::put('/looks/{id}', [LookController::class, 'update']);
    Route::delete('/looks/{id}', [LookController::class, 'destroy']);
    Route::post('/looks-assign-drafts', [LookController::class, 'afterLoginAssignDrafts']);



    Route::put('/looks-like-unlike/{look}', [LookCommentController::class, 'toggleLike']);

    Route::get('/looks-comments/{look}', [LookCommentController::class, 'index']);
    Route::post('/looks-comments/{look}', [LookCommentController::class, 'store']);
    Route::get('/comments/{id}', [LookCommentController::class, 'show']);
    Route::put('/comments/{id}', [LookCommentController::class, 'update']);
    Route::delete('/comments/{id}', [LookCommentController::class, 'destroy']);


    Route::post('/send-message', [MessageController::class, 'send']);
    Route::get('/chat-list', [MessageController::class, 'chatList']);
    Route::get('/chat-with/{userId}', [MessageController::class, 'chatWith']);

    Route::post('/follow', [FollowController::class, 'follow']);
    Route::post('/unfollow', [FollowController::class, 'unfollow']);
    Route::get('/user/{id}/followers', [FollowController::class, 'followers']);
    Route::get('/user/{id}/following', [FollowController::class, 'following']);

    Route::get('/user/block-toggle/{id}', [UserController::class, 'toggleBlock']);


    Route::get('/stripe/get-customer-id', [StripeController::class, 'createCustomer']);
    Route::get('/stripe/test/get-payment-method', [StripeController::class, 'getPaymentMethod']);
    Route::post('/stripe/add-payment-method', [StripeController::class, 'addPaymentMethod']);
    Route::post('/stripe/make-payment-customer', [StripeController::class, 'chargeCustomer']);
    Route::post('/stripe/create-connected-account', [StripeController::class, 'createConnectedAccount']);
    Route::post('/stripe/transfer-to-connected', [StripeController::class, 'transferToConnected']);
});
