<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Api\NewsletterController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Ruta para el formulario de contacto (desde tu componente React)
Route::post('/contact/send', [PageController::class, 'sendContact'])
    ->middleware('web') // Importante para CSRF token
    ->name('api.contact.send');

// Ruta de verificación de salud de la API
Route::get('/health', function () {
    return response()->json([
        'status' => 'online',
        'service' => 'SoluTech API',
        'timestamp' => now()->toDateTimeString(),
        'version' => '1.0.0'
    ]);
});

// Ruta para el Newletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->middleware('web') // ← Esto es clave para CSRF token
    ->name('api.newsletter.subscribe');