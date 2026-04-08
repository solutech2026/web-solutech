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
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->string('type'); // 'employee', 'visitor', 'resident'
            $table->string('name');
            $table->string('document_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // Para empleados
            $table->string('department')->nullable(); // Para empleados
            $table->text('bio')->nullable(); // Biografía
            $table->string('avatar')->nullable();

            // Campos para tarjeta NFC
            $table->string('nfc_card_id')->nullable()->unique();
            $table->string('bio_url')->nullable()->unique(); // URL pública de la biografía

            // Campos para visitantes
            $table->integer('companions')->default(0);
            $table->string('visit_reason')->nullable();

            // Control de acceso
            $table->timestamp('last_access_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Índices
            $table->index('nfc_card_id');
            $table->index('bio_url');
            $table->index('type');
            $table->index('document_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('persons');
    }
};
