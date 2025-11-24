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
    Schema::create('residents', function (Blueprint $table) {
        $table->id();
        $table->string('name', 120);
        $table->string('email', 120)->nullable()->unique();
        $table->string('phone', 30)->nullable();
        $table->string('unit_code', 50)->nullable(); // relasi longgar ke units.code
        $table->timestamps();
    });
}

};
