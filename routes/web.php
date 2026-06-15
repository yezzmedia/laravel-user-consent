<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use YezzMedia\Consent\Http\Controllers\ConsentController;

Route::prefix('__consent')->group(function (): void {
    Route::get('state', [ConsentController::class, 'state'])->name('consent.state');
    Route::post('grant-all', [ConsentController::class, 'grantAll'])->name('consent.grant-all');
    Route::post('revoke-all', [ConsentController::class, 'revokeAll'])->name('consent.revoke-all');
    Route::post('save', [ConsentController::class, 'save'])->name('consent.save');
    Route::post('reset', [ConsentController::class, 'reset'])->name('consent.reset');
});

Route::get('/consent/preferences', [ConsentController::class, 'preferences'])
    ->name('consent.preferences');

Route::post('/consent/preferences', [ConsentController::class, 'updatePreferences'])
    ->name('consent.preferences.update');
