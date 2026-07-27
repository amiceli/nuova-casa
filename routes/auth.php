<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', array(AuthenticatedSessionController::class, 'create'))
        ->name('login');

    Route::post('login', array(AuthenticatedSessionController::class, 'store'));
});

Route::middleware('auth')->group(function () {
    Route::post('logout', array(AuthenticatedSessionController::class, 'destroy'))
        ->name('logout');
});
