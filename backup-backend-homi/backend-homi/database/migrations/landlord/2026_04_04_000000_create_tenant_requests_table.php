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
        Schema::connection('central')->create('tenant_requests', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('name'); // Nama Perumahan
            $blueprint->string('manager_name');
            $blueprint->string('email');
            $blueprint->string('phone');
            $blueprint->string('status')->default('pending'); // pending, approved, rejected
            $blueprint->text('notes')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('tenant_requests');
    }
};
