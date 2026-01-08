<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| USER DASHBOARD (DEFAULT)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403);
})->middleware(['auth'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
// Admin routes with auth middleware
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/students', [AdminController::class, 'students'])
        ->name('students');

    Route::get('/attendance', [AdminController::class, 'attendance'])
        ->name('attendance');

    // Simulate RFID tap (for prototype)
    Route::post('/rfid/simulate', [AdminController::class, 'simulateRFID'])
        ->name('rfid.simulate');
});