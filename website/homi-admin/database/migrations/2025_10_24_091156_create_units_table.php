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
    Schema::create('units', function (Blueprint $table) {
        $table->id();
        $table->string('code', 50)->unique(); // contoh: D1-07
        $table->string('block', 20)->nullable(); // opsional
        $table->integer('floor')->nullable();   // opsional
        $table->timestamps();
    });
}

};
