<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\BioController;

//Rutas controlador Page
Route::get('/servicio', [PageController::class, 'servicios']);
Route::get('/contacto', [PageController::class, 'contact']);
// Ruta adicional para formulario POST tradicional (si la necesitas)
Route::post('/contacto/enviar', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/about-us', [PageController::class, 'about']);

// Rutas Controlador HOME
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/terminos-y-condiciones', [HomeController::class, 'terms'])->name('terms');
Route::get('/politica-de-privacidad', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/aviso-legal', [HomeController::class, 'legal'])->name('legal');

// === RUTAS API - EXCLUYENDO CSRF DIRECTAMENTE ===
Route::post('/api/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/api/health', function() { 
    return response()->json(['status' => 'ok']); 
})->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Ruta principal para el Bio (una sola vista)
Route::get('/bio', [BioController::class, 'show'])->name('bio.show');

// Ruta para la descarga del vCard
Route::get('/bio/vcard', [BioController::class, 'downloadVCard'])->name('bio.vcard');


Route::get('/bio-test', function () {
    return inertia('Bio/Show', [
        'profile' => [
            'name' => 'Herbert Test',
            'role' => 'Ingeniero',
            'summary' => 'Test summary',
            'phone' => '+123456789',
            'email' => 'test@test.com',
            'services' => []
        ]
    ]);
});
