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
        Schema::create('nfc_cards', function (Blueprint $table) {
            $table->id();
            
            // Datos de la tarjeta
            $table->string('card_code')->unique();
            $table->string('card_uid')->unique()->nullable();
            
            // Asignación de la tarjeta
            $table->foreignId('assigned_to')->nullable()->constrained('persons')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            
            // Campo de compatibilidad con código existente
            $table->foreignId('person_id')->nullable()->constrained('persons')->onDelete('set null');
            
            // Estado de la tarjeta
            $table->enum('status', ['active', 'inactive', 'lost', 'damaged'])->default('active');
            
            // Información adicional
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            
            // Control de uso
            $table->timestamp('last_used_at')->nullable();
            
            $table->timestamps();

            // Índices
            $table->index('card_code');
            $table->index('card_uid');
            $table->index('status');
            $table->index('assigned_to');
            $table->index('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('nfc_cards');
    }
};
