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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            
            // Relaciones
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('person_id')->nullable()->constrained('persons')->onDelete('set null');
            $table->foreignId('nfc_card_id')->nullable()->constrained('nfc_cards')->onDelete('set null');
            $table->foreignId('card_id')->nullable()->constrained('nfc_cards')->onDelete('set null'); // ← Agregado para compatibilidad
            
            // Datos del acceso
            $table->string('access_type')->default('entry'); // 'entry', 'exit'
            $table->string('verification_method')->default('nfc'); // 'nfc', 'manual', 'qr', 'bio_url'
            $table->timestamp('access_time');
            $table->string('status')->default('granted'); // 'granted', 'denied', 'pending'
            $table->string('gate')->nullable(); // Puerta o punto de acceso
            $table->string('ip_address')->nullable();
            $table->text('reason')->nullable(); // Razón de denegación si aplica
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable(); // ← Agregado para notas adicionales
            
            $table->timestamps();

            // Índices
            $table->index('access_time');
            $table->index('access_type');
            $table->index('status');
            $table->index('company_id');
            $table->index('person_id');
            $table->index('nfc_card_id');
            $table->index('card_id'); // ← Índice para la nueva columna
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
};
