<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('period', 7)->nullable()->index(); // "YYYY-MM" (opsional)
            $table->decimal('risk', 6, 4)->default(0);       // 0.0000 - 1.0000
            $table->boolean('predicted_delinquent')->default(false);
            $table->json('features_json')->nullable();
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_risk_scores');
    }
};