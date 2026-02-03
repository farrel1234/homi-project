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

            // periode bulanan, simpan sebagai DATE (pakai tanggal 1)
            $table->date('period')->nullable()->index();

            // probabilitas/score (0..1)
            $table->decimal('risk', 6, 4)->default(0);

            // hasil klasifikasi
            $table->boolean('predicted_delinquent')->default(false)->index();

            // fitur yang dipakai model (buat debug/audit)
            $table->json('features_json')->nullable();

            $table->timestamp('computed_at')->nullable()->index();
            $table->timestamps();

            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_risk_scores');
    }
};
