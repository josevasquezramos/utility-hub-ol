<?php

use App\Http\Controllers\ConversionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ScheduleExportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', fn () => redirect()->route('login'));

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::post('/schedule/activity', [ScheduleController::class, 'storeActivity'])->name('schedule.activity.store');
    Route::delete('/schedule/activity/{activity}', [ScheduleController::class, 'deleteActivity'])->name('schedule.activity.delete');
    Route::post('/schedule/assignment', [ScheduleController::class, 'updateAssignment'])->name('schedule.assignment.update');
    Route::post('/schedule/export-pdf', [ScheduleExportController::class, 'download'])->name('schedule.export-pdf');
    Route::post('/schedule/export-excel', [ScheduleExportController::class, 'downloadExcel'])->name('schedule.export-excel');

    Route::get('/conversions', [ConversionController::class, 'index'])->name('conversions.index');
});

require __DIR__.'/auth.php';
