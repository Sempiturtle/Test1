<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\CalendarController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\ProgramController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// PUBLIC ATTENDANCE TAP
Route::post('/attendance/tap', [AttendanceController::class, 'simulate'])->name('attendance.tap');

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
})->middleware(['auth'])->name('dashboard');

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
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Students
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

    // Cases
    Route::post('/cases', [CaseController::class, 'store'])->name('cases.store');
    Route::put('/cases/{case}', [CaseController::class, 'update'])->name('cases.update');

    Route::put('/students/{student}', [StudentController::class, 'update'])
        ->name('students.update');

    Route::delete('/students/{student}', [StudentController::class, 'destroy'])
        ->name('students.destroy');


    // Attendance Logs
    Route::get('/attendance/logs', [AttendanceController::class, 'index'])->name('attendance.logs');
    Route::get('/attendance/latest-logs', [AttendanceController::class, 'latestLogs'])->name('attendance.latestLogs');
    Route::put('/attendance/{attendance}/note', [AttendanceController::class, 'updateNote'])->name('attendance.updateNote');

    // ========== ANALYTICS ROUTES ==========
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [AnalyticsController::class, 'export'])->name('analytics.export');
    Route::post('/analytics/follow-up/{student}', [AnalyticsController::class, 'sendFollowUp'])->name('analytics.follow-up');

    // ========== AUDIT TRAIL ==========
    Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');

    // ========== CALENDAR & APPOINTMENTS ==========
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::post('/appointments', [CalendarController::class, 'store'])->name('appointments.store');

    // Programs (Settings)
    Route::resource('programs', ProgramController::class);
    Route::post('programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
});