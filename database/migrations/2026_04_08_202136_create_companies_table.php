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
            
            // Datos básicos
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['company', 'school'])->default('company');
            
            // Datos de contacto
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            
            // Identificación fiscal
            $table->string('rif')->nullable(); // RIF de la empresa/colegio
            $table->string('code')->nullable(); // Código del colegio (opcional)
            
            // Logo e imagen
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable(); // Imagen de portada
            
            // Datos específicos para colegios
            $table->enum('educational_level', ['preschool', 'primary', 'secondary', 'highschool', 'all'])->nullable();
            $table->json('grade_levels')->nullable(); // Grados que ofrece el colegio
            $table->json('sections')->nullable(); // Secciones disponibles
            
            // Horarios generales
            $table->time('opening_time')->nullable(); // Hora de apertura
            $table->time('closing_time')->nullable(); // Hora de cierre
            
            // Configuración
            $table->json('settings')->nullable();
            $table->json('contact_info')->nullable(); // Información de contacto adicional
            $table->json('social_links')->nullable(); // Redes sociales
            
            // Estado
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('type');
            $table->index('slug');
            $table->index('is_active');
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
