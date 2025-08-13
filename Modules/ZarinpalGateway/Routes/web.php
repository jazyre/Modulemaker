<?php

use Illuminate\Support\Facades\Route;
use Modules\ZarinpalGateway\Http\Controllers\ModuleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('admin/modules')->group(function() {
    // Route for the main module management page
    Route::get('/', [ModuleController::class, 'index'])->name('admin.modules.index');

    // Routes for a specific module's settings
    Route::prefix('{module}')->group(function() {
        Route::get('/settings', [ModuleController::class, 'settings'])->name('admin.modules.settings');
        Route::post('/settings', [ModuleController::class, 'updateSettings'])->name('admin.modules.settings.update');

        // Routes for managing event listeners
        Route::post('/listeners', [ModuleController::class, 'storeListener'])->name('admin.modules.listeners.store');
        Route::delete('/listeners/{listenerId}', [ModuleController::class, 'destroyListener'])->name('admin.modules.listeners.destroy');

        // Routes for managing dispatched events
        Route::post('/events', [ModuleController::class, 'storeEvent'])->name('admin.modules.events.store');
        Route::delete('/events/{eventId}', [ModuleController::class, 'destroyEvent'])->name('admin.modules.events.destroy');
    });
});
