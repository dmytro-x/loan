<?php

use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    Route::get('/clients/{id}', [\App\Modules\Client\Interfaces\Http\ClientController::class, 'show'])->name('clients.show');
    Route::post('/clients', [\App\Modules\Client\Interfaces\Http\ClientController::class, 'store'])->name('clients.store');

    Route::post('/credits/check', \App\Modules\Credit\Interfaces\Http\CreditCheckController::class)->name('credits.check');
    Route::post('/credits', \App\Modules\Credit\Interfaces\Http\CreditIssueController::class)->name('credits.issue');
});
