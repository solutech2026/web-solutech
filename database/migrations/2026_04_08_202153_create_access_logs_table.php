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
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->foreignId('person_id')->constrained('persons')->onDelete('cascade');
            $table->foreignId('nfc_card_id')->nullable()->constrained('nfc_cards')->onDelete('set null');
            $table->string('access_type'); // 'entry', 'exit'
            $table->string('verification_method'); // 'nfc', 'manual', 'qr', 'bio_url'
            $table->timestamp('access_time');
            $table->string('status'); // 'granted', 'denied', 'pending'
            $table->string('gate')->nullable(); // Puerta o punto de acceso
            $table->string('ip_address')->nullable();
            $table->text('reason')->nullable(); // Razón de denegación si aplica
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Índices
            $table->index('access_time');
            $table->index('access_type');
            $table->index('status');
            $table->index('company_id');
            $table->index('person_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('access_logs');
    }
};
