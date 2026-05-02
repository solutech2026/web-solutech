<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nfc_readers', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // DATOS BÁSICOS
            // ==========================================
            $table->string('name');
            $table->string('ubicacion')->nullable();
            $table->enum('type', ['network', 'wifi'])->default('network');
            $table->string('device_code')->nullable();      // Código identificador del dispositivo
            $table->string('serial_number')->nullable();    // Número de serie
            
            // ==========================================
            // CONFIGURACIÓN RED (IP)
            // ==========================================
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('protocol')->default('tcp');
            $table->string('mac_address')->nullable();      // MAC Address del dispositivo
            $table->string('username')->nullable();          // Usuario para autenticación
            $table->string('password')->nullable();          // Contraseña
            
            // ==========================================
            // CONFIGURACIÓN WIFI
            // ==========================================
            $table->string('ssid')->nullable();              // Nombre de la red WiFi
            $table->string('wifi_password')->nullable();     // Contraseña WiFi
            $table->string('wifi_ip_address')->nullable();   // IP estática (opcional)
            $table->integer('wifi_port')->nullable();        // Puerto
            $table->string('wifi_protocol')->default('tcp'); // Protocolo (tcp, udp, http, https)
            $table->string('wifi_mac_address')->nullable();  // MAC Address WiFi
            $table->string('wifi_username')->nullable();     // Usuario WiFi
            $table->enum('encryption', ['wpa2', 'wpa3', 'wep', 'open'])->default('wpa2'); // Tipo de encriptación
            
            // ==========================================
            // CONFIGURACIÓN GENERAL
            // ==========================================
            $table->integer('timeout')->default(30);              // Timeout en segundos
            $table->integer('retry_interval')->default(5000);     // Intervalo de reintento en ms
            $table->boolean('alert_on_disconnect')->default(true); // Alertas de desconexión
            
            // ==========================================
            // ESTADO Y CONTROL
            // ==========================================
            $table->enum('status', ['active', 'inactive', 'configuring', 'error'])->default('inactive');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_connection')->nullable();
            $table->timestamp('last_error')->nullable();
            $table->text('error_message')->nullable();
            
            // ==========================================
            // METADATOS
            // ==========================================
            $table->json('settings')->nullable();        // Configuraciones adicionales
            $table->json('metadata')->nullable();        // Metadatos del dispositivo
            
            $table->timestamps();
            
            // ==========================================
            // ÍNDICES
            // ==========================================
            $table->index('type');
            $table->index('status');
            $table->index('is_active');
            $table->index('ip_address');
            $table->index('device_code');
            $table->index('serial_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_readers');
    }
};
