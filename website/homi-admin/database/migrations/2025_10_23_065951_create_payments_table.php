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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->string('invoice_id')->unique();
        $table->string('resident_name');
        $table->string('unit')->nullable();
        $table->string('period');          // contoh: "Okt 2025"
        $table->unsignedInteger('amount'); // contoh: 150000
        $table->enum('status', ['Menunggu','Perlu cek','Valid','Invalid'])->default('Menunggu');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
