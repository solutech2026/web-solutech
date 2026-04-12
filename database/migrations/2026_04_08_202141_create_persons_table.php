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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            
            // Relación con usuario del sistema
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Relación con empresa/colegio
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            
            // ============ CATEGORIZACIÓN ============
            $table->enum('category', ['employee', 'school'])->default('employee');
            $table->enum('subcategory', ['student', 'teacher', 'administrative'])->nullable();
            
            // ============ DATOS PERSONALES ============
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('document_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            
            // ============ DATOS LABORALES (Empleados) ============
            $table->string('position')->nullable(); // Cargo
            $table->string('department')->nullable(); // Departamento
            $table->text('bio')->nullable(); // Biografía
            
            // ============ DATOS ACADÉMICOS (Estudiantes) ============
            $table->string('grade_level')->nullable(); // Grado (1ro, 2do, etc)
            $table->string('academic_year')->nullable(); // Año escolar (2024-2025)
            $table->string('period')->nullable(); // Periodo actual (first, second, third)
            $table->decimal('average_grade', 5, 2)->nullable(); // Promedio general
            $table->json('grades_documents')->nullable(); // URLs de boletines
            
            // ============ INFORMACIÓN DE EMERGENCIA ============
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            
            // ============ HORARIOS ============
            $table->json('schedule')->nullable();
            
            // ============ DATOS ESPECÍFICOS DOCENTES ============
            $table->enum('teacher_type', ['regular', 'substitute', 'special_education', 'part_time'])->nullable();
            $table->json('subjects')->nullable(); // Materias que enseña
            
            // ============ TARJETA NFC ============
            $table->string('nfc_card_id')->nullable()->unique();
            $table->string('bio_url')->nullable()->unique(); // URL pública de la biografía
            
            // ============ CAMPOS LEGADOS (Compatibilidad) ============
            $table->string('type')->nullable(); // Para compatibilidad con código anterior
            $table->integer('companions')->default(0);
            $table->string('visit_reason')->nullable();
            $table->json('metadata')->nullable();
            
            // ============ CONTROL DE ACCESO ============
            $table->timestamp('last_access_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('avatar_color')->nullable();
            
            $table->timestamps();

            // ============ ÍNDICES ============
            $table->index('category');
            $table->index('subcategory');
            $table->index('type');
            $table->index('document_id');
            $table->index('nfc_card_id');
            $table->index('bio_url');
            $table->index('company_id');
            $table->index('user_id');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('persons');
    }
};
