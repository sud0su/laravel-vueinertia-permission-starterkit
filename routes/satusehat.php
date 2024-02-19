<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CronTaskController;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/setting', [AdminController::class, 'setting'])->name('admin.setting');
});

Route::middleware('auth','role:admin')->prefix('satusehat')->group(function () {
    Route::resource('/crons', CronTaskController::class);
    Route::resource('/hospitals', HospitalController::class);
});
