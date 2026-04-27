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
        Schema::table('payment_risk_scores', function (Blueprint $table) {
            // Ubah varchar(7) jadi DATE agar konsisten dengan toDateString
            $table->date('period')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_risk_scores', function (Blueprint $table) {
            $table->string('period', 7)->change();
        });
    }
};
