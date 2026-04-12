<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\AccessController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ============================================
// VERIFICACIÓN DE SALUD
// ============================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'online',
        'service' => 'Solubase API',
        'timestamp' => now()->toDateTimeString(),
        'version' => '2.0.0'
    ]);
});

// ============================================
// FORMULARIO DE CONTACTO
// ============================================
Route::post('/contact/send', [PageController::class, 'sendContact'])
    ->middleware('web')
    ->name('api.contact.send');

// ============================================
// NEWSLETTER
// ============================================
Route::prefix('newsletter')->name('api.newsletter.')->group(function () {
    Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
    Route::get('/check', [NewsletterController::class, 'check'])->name('check');

    // Rutas protegidas (solo admin)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/subscribers', [NewsletterController::class, 'index'])->name('subscribers');
    });
});

// ============================================
// ACCESO NFC
// ============================================
Route::prefix('access')->name('api.access.')->group(function () {
    // Rutas públicas (el lector NFC llama a estas)
    Route::post('/validate', [AccessController::class, 'validateAccess'])->name('validate');
    Route::post('/reader/read', [AccessController::class, 'readFromReader'])->name('reader');

    // Rutas protegidas (requieren autenticación)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/logs', [AccessController::class, 'getLogs'])->name('logs');
        Route::get('/stats', [AccessController::class, 'getStats'])->name('stats');
        Route::get('/person/{id}/last', [AccessController::class, 'getLastAccess'])->name('person.last');
    });
});
