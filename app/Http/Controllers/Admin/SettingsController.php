<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SettingsController extends Controller
{
    /**
     * Mostrar la página de configuración
     */
    public function index()
    {
        $settings = $this->getSettings();
        $lastUpdated = Cache::get('settings_last_updated', 'Nunca');

        return view('admin.settings.index', compact('settings', 'lastUpdated'));
    }

    /**
     * Actualizar la configuración
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'general.system_name' => 'nullable|string|max:255',
                'general.timezone' => 'nullable|string',
                'general.date_format' => 'nullable|string',
                'general.language' => 'nullable|string|in:es,en,pt',
                'security.max_attempts' => 'nullable|integer|min:1|max:10',
                'security.lockout_time' => 'nullable|integer|min:1|max:60',
                'security.session_timeout' => 'nullable|integer|min:5|max:120',
                'security.session_timeout_enabled' => 'nullable|boolean',
                'security.two_factor_auth' => 'nullable|boolean',
                'notifications.email_notifications' => 'nullable|boolean',
                'notifications.access_alerts' => 'nullable|boolean',
                'notifications.notification_emails' => 'nullable|string',
                'backup.auto_backup' => 'nullable|boolean',
                'backup.backup_frequency' => 'nullable|string|in:daily,weekly,monthly',
                'backup.backup_time' => 'nullable|string',
                'integrations.whatsapp' => 'nullable|boolean'
            ]);

            // Convertir valores boolean que pueden venir como strings
            $validated = $this->normalizeBooleanValues($validated);

            // Guardar configuración
            $this->saveSettings($validated);

            // Aplicar configuración en tiempo real
            $this->applySettings($validated);

            return response()->json([
                'success' => true,
                'message' => 'Configuración actualizada correctamente'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la configuración: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalizar valores booleanos
     */
    private function normalizeBooleanValues($data)
    {
        $booleanFields = [
            'security.session_timeout_enabled',
            'security.two_factor_auth',
            'notifications.email_notifications',
            'notifications.access_alerts',
            'backup.auto_backup',
            'integrations.whatsapp'
        ];

        foreach ($booleanFields as $field) {
            $parts = explode('.', $field);
            if (count($parts) === 2) {
                $category = $parts[0];
                $key = $parts[1];
                if (isset($data[$category][$key])) {
                    $value = $data[$category][$key];
                    // Convertir a boolean
                    if (is_string($value)) {
                        $data[$category][$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Realizar respaldo manual
     */
    public function backup(Request $request)
    {
        try {
            // Crear directorio de backups si no existe
            $backupDir = storage_path('backups');
            if (!File::exists($backupDir)) {
                File::makeDirectory($backupDir, 0755, true);
            }

            // Nombre del archivo de respaldo
            $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = $backupDir . '/' . $filename;

            // Ejecutar respaldo de base de datos
            $this->performDatabaseBackup($backupPath);

            if (File::exists($backupPath)) {
                // Limpiar backups antiguos
                $this->cleanOldBackups($backupDir, 30);

                // Usar URL directa en lugar de route()
                $downloadUrl = url('/admin/settings/download-backup/' . $filename);

                return response()->json([
                    'success' => true,
                    'message' => 'Respaldo completado exitosamente',
                    'download_url' => $downloadUrl
                ]);
            } else {
                throw new \Exception('No se pudo crear el archivo de respaldo');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al realizar el respaldo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método auxiliar para respaldo de base de datos
     */
    private function performDatabaseBackup($backupPath)
    {
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        
        // Verificar si es necesario escapar la contraseña
        $escapedPassword = str_replace("'", "'\\''", $password);
        
        // Construir comando mysqldump
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
            escapeshellarg($username),
            escapeshellarg($escapedPassword),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );
        
        exec($command, $output, $returnVar);
        
        if ($returnVar !== 0) {
            $errorMessage = implode("\n", $output);
            throw new \Exception('Error al ejecutar mysqldump: ' . $errorMessage);
        }
        
        // Verificar que el archivo no esté vacío
        if (File::size($backupPath) === 0) {
            throw new \Exception('El archivo de respaldo está vacío');
        }
    }

    /**
     * Descargar archivo de respaldo
     */
    public function downloadBackup($filename)
    {
        // Validar que el filename no contenga path traversal
        $filename = basename($filename);
        
        // Validar extensión permitida
        $allowedExtensions = ['sql', 'zip', 'gz'];
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(403, 'Tipo de archivo no permitido');
        }
        
        $backupPath = storage_path('backups/' . $filename);
        
        if (File::exists($backupPath)) {
            return response()->download($backupPath, $filename, [
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate'
            ]);
        }
        
        abort(404, 'Archivo de respaldo no encontrado');
    }

    /**
     * Obtener configuración actual
     */
    private function getSettings()
    {
        $defaultSettings = [
            'system_name' => 'SoluTech Access Control',
            'timezone' => 'America/Caracas',
            'date_format' => 'd/m/Y',
            'language' => 'es',
            'max_attempts' => 5,
            'lockout_time' => 15,
            'session_timeout' => 30,
            'session_timeout_enabled' => true,
            'two_factor_auth' => false,
            'email_notifications' => true,
            'access_alerts' => false,
            'notification_emails' => '',
            'auto_backup' => true,
            'backup_frequency' => 'weekly',
            'backup_time' => '02:00',
            'whatsapp_integration' => false
        ];

        // Cargar desde caché
        $savedSettings = Cache::get('system_settings', []);
        
        // Si no hay configuraciones guardadas, guardar las predeterminadas
        if (empty($savedSettings)) {
            Cache::forever('system_settings', $defaultSettings);
            return $defaultSettings;
        }

        return array_merge($defaultSettings, $savedSettings);
    }

    /**
     * Guardar configuración
     */
    private function saveSettings($settings)
    {
        // Aplanar el array de configuración
        $flatSettings = [];
        foreach ($settings as $category => $values) {
            foreach ($values as $key => $value) {
                $flatSettings[$key] = $value;
            }
        }

        // Guardar en caché
        Cache::forever('system_settings', $flatSettings);
        Cache::forever('settings_last_updated', now()->format('d/m/Y H:i:s'));

        // Opcional: Guardar en archivo .env para algunas configuraciones
        $this->updateEnvFile($flatSettings);
        
        // Opcional: Guardar en archivo JSON como respaldo
        $this->backupSettingsToFile($flatSettings);
    }

    /**
     * Respaldar configuración a archivo JSON
     */
    private function backupSettingsToFile($settings)
    {
        $backupDir = storage_path('settings_backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        
        $backupFile = $backupDir . '/settings_' . date('Y-m-d') . '.json';
        File::put($backupFile, json_encode($settings, JSON_PRETTY_PRINT));
        
        // Limpiar backups de configuración antiguos (mantener 7 días)
        $files = File::files($backupDir);
        $cutoffDate = now()->subDays(7);
        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffDate->timestamp) {
                File::delete($file);
            }
        }
    }

    /**
     * Aplicar configuración en tiempo real
     */
    private function applySettings($settings)
    {
        // Aplicar zona horaria
        if (isset($settings['general']['timezone'])) {
            config(['app.timezone' => $settings['general']['timezone']]);
            date_default_timezone_set($settings['general']['timezone']);
        }

        // Aplicar idioma
        if (isset($settings['general']['language'])) {
            app()->setLocale($settings['general']['language']);
        }
    }

    /**
     * Actualizar archivo .env
     */
    private function updateEnvFile($settings)
    {
        // Solo actualizar configuraciones críticas
        $envUpdates = [];

        if (isset($settings['timezone'])) {
            $envUpdates['APP_TIMEZONE'] = $settings['timezone'];
        }

        if (isset($settings['language'])) {
            $envUpdates['APP_LOCALE'] = $settings['language'];
        }

        if (empty($envUpdates)) {
            return;
        }

        $envPath = base_path('.env');
        if (File::exists($envPath) && File::isWritable($envPath)) {
            $content = File::get($envPath);

            foreach ($envUpdates as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";
                
                if (preg_match($pattern, $content)) {
                    $content = preg_replace($pattern, $replacement, $content);
                } else {
                    $content .= "\n{$replacement}";
                }
            }

            File::put($envPath, $content);
        }
    }

    /**
     * Limpiar backups antiguos
     */
    private function cleanOldBackups($backupDir, $daysToKeep)
    {
        if (!File::exists($backupDir)) {
            return;
        }
        
        $files = File::files($backupDir);
        $cutoffDate = now()->subDays($daysToKeep);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffDate->timestamp) {
                File::delete($file);
            }
        }
    }
    
    /**
     * Resetear configuración a valores predeterminados
     */
    public function reset(Request $request)
    {
        try {
            $defaultSettings = [
                'system_name' => 'SoluTech Access Control',
                'timezone' => 'America/Caracas',
                'date_format' => 'd/m/Y',
                'language' => 'es',
                'max_attempts' => 5,
                'lockout_time' => 15,
                'session_timeout' => 30,
                'session_timeout_enabled' => true,
                'two_factor_auth' => false,
                'email_notifications' => true,
                'access_alerts' => false,
                'notification_emails' => '',
                'auto_backup' => true,
                'backup_frequency' => 'weekly',
                'backup_time' => '02:00',
                'whatsapp_integration' => false
            ];
            
            Cache::forever('system_settings', $defaultSettings);
            Cache::forever('settings_last_updated', now()->format('d/m/Y H:i:s'));
            
            return response()->json([
                'success' => true,
                'message' => 'Configuración restablecida a valores predeterminados'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restablecer la configuración: ' . $e->getMessage()
            ], 500);
        }
    }
}
