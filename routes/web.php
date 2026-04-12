<?php

use App\Http\Controllers\BioController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AccessControlController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NFCCardController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Api\AccessController as ApiAccessController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Public\BioController as PublicBioController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ============================================================================
// 1. RUTAS PÚBLICAS
// ============================================================================

// Home y páginas estáticas
Route::get('/', [HomeController::class, 'Index'])->name('home');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/legal', [HomeController::class, 'legal'])->name('legal');

// Servicios y acerca de
Route::get('/servicio', fn() => Inertia::render('Service/Service'))->name('servicio');
Route::get('/about-us', fn() => Inertia::render('About/About'))->name('about');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');

// Rutas públicas de Bio
Route::prefix('bio')->name('bio.')->group(function () {
    Route::get('/{url}', [App\Http\Controllers\Public\BioController::class, 'show'])->name('public');
    Route::get('/{url}/data', [App\Http\Controllers\Public\BioController::class, 'getData'])->name('public.data');
    Route::get('/{url}/vcard', [App\Http\Controllers\Public\BioController::class, 'downloadVCard'])->name('public.vcard');
});

// ============================================================================
// 2. API PÚBLICAS (NFC, Newsletter, Contacto, Health)
// ============================================================================

Route::prefix('api')->name('api.')->group(function () {

    // 2.1 Health Check
    Route::get('/health', fn() => response()->json([
        'status' => 'online',
        'service' => 'Solubase API',
        'timestamp' => now()->toDateTimeString(),
        'version' => '2.0.0'
    ]));

    // 2.2 Acceso NFC
    Route::prefix('access')->name('access.')->group(function () {
        Route::post('/validate', [ApiAccessController::class, 'validateAccess'])->name('validate');
        Route::get('/logs', [ApiAccessController::class, 'getLogs'])->name('logs');
        Route::get('/stats', [ApiAccessController::class, 'getStats'])->name('stats');
        Route::post('/reader/read', [ApiAccessController::class, 'readFromReader'])->name('reader');
        Route::get('/person/{id}/last', [ApiAccessController::class, 'getLastAccess'])->name('person.last');
    });

    // 2.3 Newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
        Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('/check', [NewsletterController::class, 'check'])->name('check');
    });

    // 2.4 Contacto
    Route::post('/contact/send', [PageController::class, 'sendContact'])->name('contact.send');
});

// ============================================================================
// 3. RUTAS DE AUTENTICACIÓN BREEZE
// ============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================================
// 4. RUTAS DEL ADMINISTRADOR
// ============================================================================

Route::prefix('admin')->name('admin.')->group(function () {

    // 4.1 Autenticación Admin
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.submit');
        Route::post('/logout', 'logout')->name('logout');
    });

    // 4.2 Rutas Protegidas
    Route::middleware(['auth'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Control de Acceso
        Route::prefix('access-control')->name('access-control.')->controller(AccessControlController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}/assign-nfc', 'assignNFC')->name('assign-nfc');
            Route::post('/{id}/unassign-nfc', 'unassignNFC')->name('unassign-nfc');
            Route::get('/export/logs', 'exportLogs')->name('export-logs');
            Route::get('/logs/data', 'getAccessLogs')->name('logs-data');
            Route::get('/stats/data', 'getStats')->name('stats');
        });

        // Tarjetas NFC
        Route::prefix('nfc-cards')->name('nfc-cards.')->controller(NFCCardController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/{id}/assign', 'assignForm')->name('assign');
            Route::put('/{id}/assign', 'assign')->name('assign.store');
            Route::post('/{id}/unassign', 'unassign')->name('unassign');
            Route::get('/export/csv', 'export')->name('export');
            Route::get('/reader/config', 'readerConfig')->name('reader');
            Route::post('/reader/save-config', 'saveReaderConfig')->name('save-config');
            Route::get('/reader/get-config', 'getReaderConfig')->name('get-config');
            Route::get('/scan-network', 'scanNetworkDevices')->name('scan-network');
            Route::get('/paired-devices', 'getPairedDevices')->name('paired-devices');
            Route::post('/pair-device', 'pairDevice')->name('pair-device');
            Route::delete('/unpair-device', 'unpairDevice')->name('unpair-device');
            Route::post('/reader/test-wired', 'testWiredConnection')->name('test-wired');
            Route::post('/reader/test-network', 'testNetworkConnection')->name('test-network');
            Route::post('/reader/read-card', 'readCard')->name('read-card');
            Route::get('/reader/generate-qr', 'generateQRCode')->name('generate-qr');
        });

        // Personas
        Route::resource('persons', PersonController::class)->names([
            'index'   => 'persons.index',
            'create'  => 'persons.create',
            'store'   => 'persons.store',
            'show'    => 'persons.show',
            'edit'    => 'persons.edit',
            'update'  => 'persons.update',
            'destroy' => 'persons.destroy',
        ]);

        Route::prefix('persons')->name('persons.')->controller(PersonController::class)->group(function () {
            Route::get('/export', 'export')->name('export');
            Route::get('/search', 'search')->name('search');
            Route::get('/{id}/access-logs', 'getAccessLogs')->name('access-logs');
            Route::put('/{id}/assign-nfc', 'assignNfc')->name('assign-nfc');
            Route::post('/{id}/unassign-nfc', 'unassignNfc')->name('unassign-nfc');
        });

        // Usuarios
        Route::prefix('users')->name('users.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'users')->name('index');
            Route::post('/store', 'storeUser')->name('store');
            Route::put('/{id}', 'updateUser')->name('update');
            Route::delete('/{id}', 'deleteUser')->name('delete');
        });

        // Reportes
        Route::prefix('reports')->name('reports.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'reports')->name('index');
            Route::post('/generate', 'generateReport')->name('generate');
        });

        // Configuración
        Route::prefix('settings')->name('settings.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'settings')->name('index');
            Route::post('/update', 'updateSettings')->name('update');
        });

        // Perfil Admin
        Route::prefix('profile')->name('profile.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'profile')->name('index');
            Route::put('/update', 'updateProfile')->name('update');
            Route::put('/password', 'updatePassword')->name('password');
            Route::post('/logout-other-sessions', 'logoutOtherSessions')->name('logout-sessions');
        });
    });
});

// ============================================================================
// 5. RUTAS DE DEBUG / BOOST (Ignorar)
// ============================================================================

Route::any('/_boost/{any}', fn() => response()->json(['message' => 'Not found'], 404))
    ->where('any', '.*');

// ============================================================================
// 6. ARCHIVOS ADICIONALES
// ============================================================================

require __DIR__ . '/auth.php';
