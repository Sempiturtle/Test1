<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403);
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /* =====================
         * DASHBOARD
         * ===================== */
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        /* =====================
         * RFID TAP (USB / Hidden Input)
         * ===================== */
        Route::post('/rfid/tap', [AdminController::class, 'rfidTap'])
            ->name('rfid.tap');

        /* =====================
         * STUDENTS
         * ===================== */
        Route::get('/students', [StudentController::class, 'index'])
            ->name('students.index');

        Route::post('/students', [StudentController::class, 'store'])
            ->name('students.store');

        /* =====================
         * ATTENDANCE
         * ===================== */
        Route::get('/attendance/logs', [AttendanceController::class, 'index'])
            ->name('attendance.logs');

        Route::post('/attendance/simulate', [AttendanceController::class, 'simulate'])
            ->name('attendance.simulate');

        // 🔴 THIS WAS MISSING (AJAX live updates)
        Route::get('/attendance/latest-logs', [AttendanceController::class, 'latestLogs'])
            ->name('attendance.latestLogs');
    });
