<?php


use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('satusehat')->group(function () {
    // Route::get('token', [\Razinal\Satusehatsync\Http\Controllers\FhirController::class, 'token'])->name('token');

});
