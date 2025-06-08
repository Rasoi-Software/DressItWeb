<?php 
use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PasswordOtpController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password-otp', [PasswordOtpController::class, 'sendOtp']);
Route::post('/reset-password-otp', [PasswordOtpController::class, 'resetWithOtp']);
