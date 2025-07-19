<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\API\StripeController;
use App\Http\Controllers\Admin\LookController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::resource('users', UserController::class);
    Route::resource('cities', CityController::class);
    Route::post('cities/import', [CityController::class, 'import'])->name('cities.import');
    Route::get('cities-export', [CityController::class, 'export'])->name('cities.export');
    Route::get('looks/', [LookController::class, 'index'])->name('looks.index');
    Route::get('looks/{id}', [LookController::class, 'show'])->name('looks.show');
    Route::delete('looks/{id}', [LookController::class, 'destroy'])->name('looks.destroy');
});


Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    });
});


Route::post('/save-payment-method', [StripeController::class, 'savePaymentMethod'])->name('save.payment.method');
Route::view('/test/card', 'testcard'); // show form
Route::get('/test-s3', function () {
    try {
        $filePath = 'test.txt';
        $fileContent = 'Hello from Laravel!';

        // Upload file with 'public' visibility
        $success = Storage::disk('s3')->put($filePath, $fileContent, 'public');

        if (!$success) {
            return response()->json(['success' => false, 'message' => 'Upload failed']);
        }

        // Get the public URL of the file
        $url = Storage::disk('s3')->url($filePath);

        return response()->json([
            'success' => true,
            'url' => $url,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'S3 Error: ' . $e->getMessage(),
        ]);
    }
});
