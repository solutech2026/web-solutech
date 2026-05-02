<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            
            // ==========================================
            // DATOS BÁSICOS
            // ==========================================
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['company', 'school', 'ngo_rescue', 'government'])->default('company');
            
            // ==========================================
            // DATOS DE CONTACTO
            // ==========================================
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // ==========================================
            // UBICACIÓN
            // ==========================================
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Venezuela');
            
            // ==========================================
            // IDENTIFICACIÓN FISCAL
            // ==========================================
            $table->string('tax_id')->nullable(); // RIF / NIT
            $table->string('code')->nullable(); // Código de la institución
            
            // ==========================================
            // IMÁGENES
            // ==========================================
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable(); // Imagen de portada
            
            // ==========================================
            // CAMPOS ESPECÍFICOS PARA EMPRESA
            // ==========================================
            $table->string('industry')->nullable(); // Sector/Industria
            $table->string('size')->nullable(); // Tamaño de la empresa
            
            // ==========================================
            // CAMPOS ESPECÍFICOS PARA COLEGIO
            // ==========================================
            $table->json('levels')->nullable(); // Niveles educativos (primaria, media, etc)
            $table->json('shifts')->nullable(); // Jornadas (mañana, tarde, noche)
            $table->string('principal')->nullable(); // Director/Directora
            
            // ==========================================
            // CAMPOS ESPECÍFICOS PARA ONG DE RESCATE
            // ==========================================
            $table->string('rescue_type')->nullable(); // Tipo (Bomberos, PC, Cruz Roja, etc)
            $table->string('emergency_line')->nullable(); // Línea de emergencia
            $table->text('coverage_area')->nullable(); // Área de cobertura
            
            // ==========================================
            // CAMPOS ESPECÍFICOS PARA GOBIERNO
            // ==========================================
            $table->enum('government_level', ['national', 'regional', 'municipal', 'parish'])->nullable();
            $table->enum('government_branch', ['executive', 'legislative', 'judicial', 'citizen', 'electoral'])->nullable();
            $table->string('government_entity_type')->nullable(); // Tipo de ente (ministerio, gobernación, etc)
            
            // ==========================================
            // HORARIOS GENERALES
            // ==========================================
            $table->time('opening_time')->nullable(); // Hora de apertura
            $table->time('closing_time')->nullable(); // Hora de cierre
            
            // ==========================================
            // CONFIGURACIÓN
            // ==========================================
            $table->json('settings')->nullable();
            $table->json('contact_info')->nullable(); // Información de contacto adicional
            $table->json('social_links')->nullable(); // Redes sociales
            
            // ==========================================
            // ESTADO
            // ==========================================
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // ==========================================
            // ÍNDICES
            // ==========================================
            $table->index('type');
            $table->index('slug');
            $table->index('is_active');
            $table->index('government_level');
            $table->index('rescue_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
};
