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
            $table->string('card_code')->unique();
            $table->foreignId('person_id')->nullable()->constrained('persons')->onDelete('set null');
            $table->string('status')->default('active'); // active, inactive, lost, damaged
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Índices
            $table->index('card_code');
            $table->index('status');
            $table->index('person_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('nfc_cards');
    }
};
