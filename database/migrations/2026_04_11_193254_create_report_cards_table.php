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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            
            // Relación con la persona (estudiante)
            $table->foreignId('person_id')->constrained('persons')->onDelete('cascade');
            
            // Datos del boletín
            $table->enum('period', ['first', 'second', 'third']);
            $table->string('academic_year');
            $table->string('grade_level');
            
            // Archivo
            $table->string('file_path');
            $table->string('file_name');
            
            // Calificaciones
            $table->decimal('average', 5, 2)->nullable();
            $table->json('subjects_grades')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index('person_id');
            $table->index('period');
            $table->index('academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('report_cards');
    }
};
