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
            
            // Datos básicos
            $table->string('name');
            $table->enum('type', ['network', 'wifi'])->default('network');
            
            // Configuración RED (IP)
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('protocol')->default('tcp');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            
            // Configuración WIFI
            $table->string('ssid')->nullable();
            $table->string('wifi_ip_address')->nullable();
            $table->integer('wifi_port')->nullable();
            $table->string('wifi_protocol')->default('tcp');
            $table->string('wifi_username')->nullable();
            $table->string('wifi_password')->nullable();
            
            // Información adicional
            $table->string('ubicacion')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_connection')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_readers');
    }
};
