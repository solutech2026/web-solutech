<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NFCCardController;
use App\Http\Controllers\Admin\AccessControlController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// ============ RUTAS PÚBLICAS CON INERTIA/REACT ============
Route::get('/', [HomeController::class, 'Index'])->name('home');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/legal', [HomeController::class, 'legal'])->name('legal');

Route::get('/servicio', function () {
    return Inertia\Inertia::render('Service/Service');
})->name('servicio');

Route::get('/about-us', function () {
    return Inertia\Inertia::render('About/About');
})->name('about');

Route::get('/contacto', function () {
    return Inertia\Inertia::render('Contact/Contact');
})->name('contacto');

// ============ RUTAS PÚBLICAS (Bio URLs) ============
Route::get('/bio', [App\Http\Controllers\Public\BioController::class, 'show'])->name('public.bio');

// ============ RUTAS API PARA ACCESO NFC ============
Route::prefix('api')->group(function () {
    Route::post('/access/validate', [App\Http\Controllers\Api\AccessController::class, 'validateAccess'])->name('api.access.validate');
    Route::get('/access/logs', [App\Http\Controllers\Api\AccessController::class, 'getLogs'])->name('api.access.logs');
});

// ============ RUTAS DE AUTENTICACIÓN BREEZE ============
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============ RUTAS DEL ADMINISTRADOR ============
Route::prefix('admin')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Panel protegido
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // ============ CONTROL DE ACCESO ============
        Route::prefix('access-control')->name('admin.access-control.')->group(function () {
            Route::get('/', [AccessControlController::class, 'index'])->name('index');
            Route::get('/create', [AccessControlController::class, 'create'])->name('create');
            Route::post('/store', [AccessControlController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AccessControlController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AccessControlController::class, 'update'])->name('update');
            Route::delete('/{id}', [AccessControlController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/assign-nfc', [AccessControlController::class, 'assignNFC'])->name('assign-nfc');
            Route::put('/{id}/unassign-nfc', [AccessControlController::class, 'unassignNFC'])->name('unassign-nfc');
        });

        // ============ TARJETAS NFC ============
        Route::prefix('nfc-cards')->name('admin.nfc-cards.')->group(function () {
            Route::get('/', [NFCCardController::class, 'index'])->name('index');
            Route::get('/create', [NFCCardController::class, 'create'])->name('create');
            Route::post('/store', [NFCCardController::class, 'store'])->name('store');
            Route::get('/{id}', [NFCCardController::class, 'show'])->name('show');
            Route::get('/{id}/assign', [NFCCardController::class, 'assignForm'])->name('assign');
            Route::put('/{id}/assign', [NFCCardController::class, 'assign'])->name('assign.store');
            Route::delete('/{id}', [NFCCardController::class, 'destroy'])->name('destroy');
        });

        // ============ PERSONAS ============
        Route::prefix('persons')->name('admin.persons.')->group(function () {
            Route::get('/', [DashboardController::class, 'persons'])->name('index');
            Route::get('/{id}/edit', [DashboardController::class, 'getPerson'])->name('edit');
            Route::post('/store', [DashboardController::class, 'storePerson'])->name('store');
            Route::put('/{id}', [DashboardController::class, 'updatePerson'])->name('update');
            Route::put('/{id}/assign-nfc', [DashboardController::class, 'assignNFCToPerson'])->name('assign-nfc');
            Route::delete('/{id}', [DashboardController::class, 'deletePerson'])->name('delete');
            Route::post('/{id}/unassign-nfc', [DashboardController::class, 'unassignNFCCard'])->name('unassign-nfc');
        });
        Route::get('/person/{id}', [DashboardController::class, 'personDetail'])->name('admin.person.detail');

        // ============ USUARIOS ============
        Route::prefix('users')->name('admin.users.')->group(function () {
            Route::get('/', [DashboardController::class, 'users'])->name('index');
            Route::post('/store', [DashboardController::class, 'storeUser'])->name('store');
            Route::put('/{id}', [DashboardController::class, 'updateUser'])->name('update');
            Route::delete('/{id}', [DashboardController::class, 'deleteUser'])->name('delete');
        });

        // ============ REPORTES ============
        Route::prefix('reports')->name('admin.reports.')->group(function () {
            Route::get('/', [DashboardController::class, 'reports'])->name('index');
            Route::post('/generate', [DashboardController::class, 'generateReport'])->name('generate');
        });

        // ============ CONFIGURACIÓN ============
        Route::prefix('settings')->name('admin.settings.')->group(function () {
            Route::get('/', [DashboardController::class, 'settings'])->name('index');
            Route::post('/update', [DashboardController::class, 'updateSettings'])->name('update');
        });

        // ============ PERFIL ============
        Route::prefix('profile')->name('admin.profile.')->group(function () {
            Route::get('/', [DashboardController::class, 'profile'])->name('index');
            Route::put('/update', [DashboardController::class, 'updateProfile'])->name('update');
            Route::put('/password', [DashboardController::class, 'updatePassword'])->name('password');
            Route::post('/logout-other-sessions', [DashboardController::class, 'logoutOtherSessions'])->name('logout-sessions');
        });
    });
});

// Ignorar rutas de debug
Route::any('/_boost/{any}', function () {
    return response()->json(['message' => 'Not found'], 404);
})->where('any', '.*');

require __DIR__ . '/auth.php';
