<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bio_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // Para la URL personalizada
            $table->string('name');
            $table->string('role');
            $table->text('summary');
            $table->string('phone');
            $table->string('email');
            $table->string('photo_path')->nullable();

            // Campos Flexibles
            $table->json('services'); // [ {"icon": "Server", "label": "Soporte N1"}, ... ]
            $table->json('social_links'); // [ {"platform": "instagram", "url": "..."}, ... ]

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bio_profiles');
    }
};
