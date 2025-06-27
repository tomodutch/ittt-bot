<?php

use App\Http\Controllers\TriggerController;
use App\Http\Controllers\TriggerExecutionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('triggers', TriggerController::class);
    Route::get('triggers/{triggerId}/executions/{executionId}', [TriggerExecutionController::class, 'show'])
        ->name('executions.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
