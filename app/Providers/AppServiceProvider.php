<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar configuración de base de datos en producción
        if ($this->app->environment('production')) {
            Config::set('database.default', 'pgsql');
            
            // Asegurar que la conexión pgsql tenga los valores correctos
            Config::set('database.connections.pgsql', [
                'driver' => 'pgsql',
                'host' => 'ep-dawn-dream-anwde4wp-pooler.c-6.us-east-1.aws.neon.tech',
                'port' => '5432',
                'database' => 'neondb',
                'username' => 'neondb_owner',
                'password' => 'npg_fCVoO14WUlJS',
                'charset' => 'utf8',
                'prefix' => '',
                'prefix_indexes' => true,
                'search_path' => 'public',
                'sslmode' => 'require',
            ]);
        }
    }
}
