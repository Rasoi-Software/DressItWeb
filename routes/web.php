<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CityController;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        Route::resource('users', UserController::class);
        Route::resource('cities', CityController::class);
        Route::post('cities/import', [CityController::class, 'import'])->name('cities.import');
        Route::get('cities-export', [CityController::class, 'export'])->name('cities.export');
    });


Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    });
});



Route::get('/test-s3', function () {
    try {
        $filePath = 'test.txt';
        $fileContent = 'Hello from Laravel!';

        // Upload file to S3
        Storage::disk('s3')->put($filePath, $fileContent);

        // Get file content back
        $retrievedContent = Storage::disk('s3')->get($filePath);

        // Encode it in base64
        $base64 = base64_encode($retrievedContent);

        return response()->json([
            'success' => true,
            'base64' => $base64
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'S3 Connection Failed: ' . $e->getMessage()
        ]);
    }
});

use App\Events\MessageSent;

Route::get('/test-laravel-pusher', function () {
    $message = [
        'from_user_id' => 1,
        'to_user_id' => 2,
        'text' => 'Hello from Laravel!',
        'created_at' => now()->toDateTimeString(),
    ];

    broadcast(new MessageSent($message))->toOthers(); // for Echo
    // OR simply:
    event(new MessageSent($message));

    return '✅ Message broadcasted!';
});

