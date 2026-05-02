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
            
            // Relación con empresa/colegio/institución
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            
            // ============ CATEGORIZACIÓN PRINCIPAL ============
            $table->enum('institution_type', ['company', 'school', 'ngo_rescue', 'government'])->default('company');
            $table->enum('subcategory', ['student', 'teacher', 'administrative'])->nullable();
            
            // ============ DATOS PERSONALES ============
            $table->string('name');
            $table->string('lastname')->nullable();
            $table->string('document_id')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('photo_url')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('birth_date')->nullable();
            $table->string('avatar_color')->nullable();
            
            // ============ LOGO DE ORGANIZACIÓN ============
            $table->string('organization_logo')->nullable(); // Logo de la empresa/ONG/gobierno
            
            // ============ DATOS LABORALES (Empleados) ============
            $table->string('position')->nullable(); // Cargo
            $table->string('department')->nullable(); // Departamento
            $table->text('bio')->nullable(); // Biografía
            
            // ============ DATOS ACADÉMICOS (Estudiantes) ============
            $table->string('grade_level')->nullable(); // Grado (1er_grado, 2do_grado, etc)
            $table->string('section')->nullable(); // Sección (A, B, C, Única)
            $table->string('academic_year')->nullable(); // Año escolar (2024-2025)
            $table->enum('period', ['first', 'second', 'third'])->nullable(); // Periodo actual
            $table->decimal('average_grade', 5, 2)->nullable(); // Promedio general
            
            // ============ BOLETINES DE NOTAS ============
            $table->string('grade_report_first')->nullable(); // Boletín 1er lapso
            $table->string('grade_report_second')->nullable(); // Boletín 2do lapso
            $table->string('grade_report_third')->nullable(); // Boletín 3er lapso
            
            // ============ INFORMACIÓN DE EMERGENCIA (para todos) ============
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('emergency_relationship')->nullable(); // Parentesco
            $table->string('blood_type')->nullable(); // Tipo de sangre
            $table->text('allergies')->nullable();
            $table->text('medical_conditions')->nullable();
            
            // ============ HORARIOS ============
            $table->json('schedule')->nullable();
            
            // ============ DATOS ESPECÍFICOS DOCENTES ============
            $table->enum('teacher_type', ['regular', 'substitute', 'special_education', 'part_time'])->nullable();
            $table->json('subjects')->nullable(); // Materias que enseña
            
            // ============ DATOS ESPECÍFICOS ONG DE RESCATE (Estilo ORH) ============
            $table->string('rescue_member_number')->nullable(); // Número de miembro
            $table->string('rescue_member_category')->nullable(); // Categoría (Operativo, Técnico, etc)
            $table->date('rescue_expiry_date')->nullable(); // Fecha de vencimiento
            $table->string('rescue_specialty_area')->nullable(); // Especialidad/Área
            $table->text('rescue_certifications')->nullable(); // Certificaciones
            $table->string('rescue_organization_type')->nullable(); // Tipo (Bomberos, PC, Cruz Roja)
            $table->string('rescue_rank')->nullable(); // Rango/Jerarquía
            $table->text('rescue_specialties')->nullable(); // Especialidades
            
            // ============ DATOS ESPECÍFICOS GUBERNAMENTALES ============
            $table->string('government_level')->nullable(); // Nivel (national, regional, municipal, parish)
            $table->string('government_branch')->nullable(); // Rama (executive, legislative, judicial, citizen, electoral)
            $table->string('government_entity')->nullable(); // Ministerio/Ente
            $table->string('government_position')->nullable(); // Cargo
            $table->string('government_card_number')->nullable(); // Número de carnet
            $table->date('government_joining_date')->nullable(); // Fecha de ingreso
            
            // ============ TARJETA NFC ============
            $table->string('nfc_card_id')->nullable()->unique();
            $table->string('bio_url')->nullable()->unique(); // URL pública de la biografía
            
            // ============ CAMPOS LEGADOS (Compatibilidad) ============
            $table->string('category')->nullable(); // Para compatibilidad con código anterior
            $table->string('type')->nullable(); // Para compatibilidad con código anterior
            $table->integer('companions')->default(0);
            $table->string('visit_reason')->nullable();
            $table->json('metadata')->nullable();
            
            // ============ CONTROL DE ACCESO ============
            $table->timestamp('last_access_at')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();

            // ============ ÍNDICES ============
            $table->index('institution_type');
            $table->index('subcategory');
            $table->index('document_id');
            $table->index('nfc_card_id');
            $table->index('bio_url');
            $table->index('company_id');
            $table->index('user_id');
            $table->index('email');
            $table->index('rescue_member_number');
            $table->index('government_card_number');
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
