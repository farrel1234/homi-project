<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ml_nb_models')) {
            return;
        }

        Schema::create('ml_nb_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120)->index();
            $table->json('model_json');
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // aman: jangan drop biar gak ngilangin model yang udah pernah kepake
        // Schema::dropIfExists('ml_nb_models');
    }
};
