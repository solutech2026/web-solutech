<?php

use App\Http\Controllers\BioController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AccessControlController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NFCCardController;
use App\Http\Controllers\Admin\NFCReaderController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PersonController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Api\AccessController as ApiAccessController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Public\BioController as PublicBioController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ============================================================================
// 1. RUTAS PÚBLICAS
// ============================================================================

Route::get('/', [HomeController::class, 'Index'])->name('home');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/legal', [HomeController::class, 'legal'])->name('legal');

Route::get('/servicio', fn() => Inertia::render('Service/Service'))->name('servicio');
Route::get('/about-us', fn() => Inertia::render('About/About'))->name('about');
Route::get('/contacto', [ContactController::class, 'index'])->name('contacto');

// Rutas públicas de Bio (perfiles públicos)
Route::prefix('bio')->name('bio.')->group(function () {
    Route::get('/{url}', [PublicBioController::class, 'show'])->name('public');
    Route::get('/{url}/data', [PublicBioController::class, 'getData'])->name('public.data');
    Route::get('/{url}/vcard', [PublicBioController::class, 'downloadVCard'])->name('public.vcard');
    Route::get('/{url}/redirect', [PublicBioController::class, 'redirectToProfile'])->name('redirect');
});

// ============================================================================
// 2. API PÚBLICAS
// ============================================================================

Route::prefix('api')->name('api.')->group(function () {
    // Health Check
    Route::get('/health', fn() => response()->json([
        'status' => 'online',
        'service' => 'Solubase API',
        'timestamp' => now()->toDateTimeString(),
        'version' => '2.0.0'
    ]));

    // Acceso NFC
    Route::prefix('access')->name('access.')->group(function () {
        Route::post('/validate', [ApiAccessController::class, 'validateAccess'])->name('validate');
        Route::get('/logs', [ApiAccessController::class, 'getLogs'])->name('logs');
        Route::get('/stats', [ApiAccessController::class, 'getStats'])->name('stats');
        Route::post('/reader/read', [ApiAccessController::class, 'readFromReader'])->name('reader');
        Route::get('/person/{id}/last', [ApiAccessController::class, 'getLastAccess'])->name('person.last');
    });

    // Newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
        Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        Route::get('/check', [NewsletterController::class, 'check'])->name('check');
    });

    // Contacto
    Route::post('/contact/send', [PageController::class, 'sendContact'])->name('contact.send');
});

// ============================================================================
// 3. RUTAS DE AUTENTICACIÓN BREEZE
// ============================================================================

require __DIR__ . '/auth.php';

// Rutas protegidas con autenticación y verificación de email
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Rutas de perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================================
// 4. RUTAS DEL ADMINISTRADOR
// ============================================================================

Route::prefix('admin')->name('admin.')->group(function () {
    // Autenticación Admin
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.submit');
        Route::post('/logout', 'logout')->name('logout');
    });

    // Rutas protegidas del admin
    Route::middleware(['auth'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Empresas y Colegios
        Route::resource('companies', CompanyController::class);

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
            Route::post('/', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::get('/{id}/assign', 'assignForm')->name('assign.form');
            Route::post('/{id}/assign', 'assign')->name('assign');
            Route::post('/{id}/unassign', 'unassign')->name('unassign');
            Route::get('/export/csv', 'export')->name('export');
            Route::get('/dispositivos', 'dispositivosIndex')->name('dispositivos');
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

        // Usuarios del Sistema
        Route::prefix('users')->name('users.')->controller(DashboardController::class)->group(function () {
            Route::get('/', 'users')->name('index');
            Route::post('/store', 'storeUser')->name('store');
            Route::put('/{id}', 'updateUser')->name('update');
            Route::delete('/{id}', 'deleteUser')->name('delete');
        });

        // Reportes
        Route::prefix('reports')->name('reports.')->controller(ReportController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/generate', 'generateReport')->name('generate');
            Route::post('/export/access', 'exportAccessCsv')->name('export.access');
            Route::post('/export/persons', 'exportPersonsCsv')->name('export.persons');
            Route::post('/export/cards', 'exportCardsCsv')->name('export.cards');
            Route::get('/export/pdf/{type}', 'exportPdf')->name('export.pdf');
            Route::get('/statistics', 'getStatistics')->name('statistics');
        });

        // Empresas activas (para selects)
        Route::get('/companies/active', [CompanyController::class, 'getActiveCompanies'])->name('companies.active');

        // Configuración del Sistema
        Route::prefix('settings')->name('settings.')->controller(SettingsController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/update', 'update')->name('update');
            Route::post('/backup', 'backup')->name('backup');
            Route::post('/reset', 'reset')->name('reset');
            Route::get('/download-backup/{filename}', 'downloadBackup')
                ->name('download-backup')
                ->where('filename', '.*');
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
// 5. RUTAS PARA LECTORES NFC
// ============================================================================

Route::prefix('lectores')->name('lectores.')->group(function () {
    Route::get('/', [NFCReaderController::class, 'index'])->name('index');
    Route::get('/nuevo', [NFCReaderController::class, 'config'])->name('nuevo');
    Route::get('/{id}/editar', [NFCReaderController::class, 'config'])->name('editar');
    Route::post('/guardar/{id?}', [NFCReaderController::class, 'save'])->name('guardar');
    Route::delete('/{id}', [NFCReaderController::class, 'delete'])->name('eliminar');
    Route::post('/{id}/test', [NFCReaderController::class, 'test'])->name('test');
});

// ============================================================================
// 6. RUTAS DE PRUEBA Y DEBUG (SOLO PARA DESARROLLO)
// ============================================================================

if (app()->environment('local')) {
    Route::get('/prueba', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'OK - Ruta funciona correctamente',
            'timestamp' => now()->toDateTimeString()
        ]);
    });

    Route::get('/prueba-vista', function () {
        return '<h1>✅ Ruta de prueba funcionando!</h1><p>Si ves esto, las rutas están correctamente configuradas.</p>';
    });

    Route::get('/test-nuevo', function () {
        return 'Ruta de prueba funcionando';
    })->name('test.nuevo');
}

// ============================================================================
// 7. RUTA CATCH-ALL PARA DEBUG (Mantener al final)
// ============================================================================

Route::any('/_boost/{any}', fn() => response()->json(['message' => 'Not found'], 404))
    ->where('any', '.*');
