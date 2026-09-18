<?php

use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::redirect('settings', '/settings/profile');

    Route::get('settings/profile', array(ProfileController::class, 'edit'))->name('profile.edit');
    Route::get('settings/profile/export.json', array(ProfileController::class, 'exportJson'))->name('profile.export.json');
    Route::get('settings/profile/export-browser.json', array(ProfileController::class, 'exportBrowser'))->name('profile.export.browser');
    Route::get('settings/profile/export.html', array(ProfileController::class, 'exportHtml'))->name('profile.export.html');
    Route::get('settings/profile/export-styled.html', array(ProfileController::class, 'exportStyledHtml'))->name('profile.export.styled');
    Route::delete('settings/profile', array(ProfileController::class, 'destroy'))->name('profile.destroy');

    Route::get('settings/appearance', function () {
        return Inertia::render('settings/Appearance');
    })->name('appearance');
});
