<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CronTaskController;
use App\Http\Controllers\DetailController;
use App\Http\Controllers\HospitalController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/setting', [AdminController::class, 'setting'])->name('admin.setting');
});

Route::middleware('auth','role:admin')->prefix('satusehat')->group(function () {
    Route::resource('/crons', CronTaskController::class);
    Route::resource('/hospitals', HospitalController::class);

    Route::get('/detail/{id}', [DetailController::class, 'index'])->name('detail');
    Route::get('/detail/{id}/service/{cronid}/{service}', [DetailController::class, 'services'])->name('services');

    // get return json datatables
    Route::get('/datatablejson', [DetailController::class, 'getDataTables'])->name('datatablejson');
    Route::get('json/{id}/{service?}', [DetailController::class, 'getJsonId'])->name('json');

    // sync data ke satu sehat
    Route::get('sync/{cronid}/{resourceType?}/{rsid?}', [DetailController::class, 'syncDataTable'])->name('cronrsk.sync');


});
