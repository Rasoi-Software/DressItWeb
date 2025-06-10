<?php 
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PasswordOtpController;
use App\Http\Controllers\API\LookController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetWithOtp']);




Route::middleware('auth:sanctum')->group(function () {

    Route::post('/update-profile', [UserController::class, 'updateProfile']);


    Route::get('/looks', [LookController::class, 'index']);
    Route::post('/looks', [LookController::class, 'store']);
    Route::get('/looks/{id}', [LookController::class, 'show']);
    Route::put('/looks/{id}', [LookController::class, 'update']);
    Route::delete('/looks/{id}', [LookController::class, 'destroy']);
});