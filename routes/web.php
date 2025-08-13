<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ModuleController;

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

// --- Module Management Routes ---
Route::prefix('admin/modules')->middleware('web')->name('admin.modules.')->group(function() {
    Route::get('/', [ModuleController::class, 'index'])->name('index');
    Route::post('/import', [ModuleController::class, 'import'])->name('import');

    Route::prefix('{module}')->group(function() {
        Route::get('/settings', [ModuleController::class, 'settings'])->name('settings');
        Route::post('/settings', [ModuleController::class, 'updateSettings'])->name('settings.update');
        Route::get('/export', [ModuleController::class, 'export'])->name('export');
        Route::get('/enable', [ModuleController::class, 'enable'])->name('enable');
        Route::get('/disable', [ModuleController::class, 'disable'])->name('disable');

        Route::post('/listeners', [ModuleController::class, 'storeListener'])->name('listeners.store');
        Route::delete('/listeners/{listenerId}', [ModuleController::class, 'destroyListener'])->name('listeners.destroy');

        Route::post('/events', [ModuleController::class, 'storeEvent'])->name('events.store');
        Route::delete('/events/{eventId}', [ModuleController::class, 'destroyEvent'])->name('events.destroy');
    });
});

// --- Payment Callback Route ---
Route::get('/payment/callback', [ModuleController::class, 'callback'])->name('payment.callback');
