<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;

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
