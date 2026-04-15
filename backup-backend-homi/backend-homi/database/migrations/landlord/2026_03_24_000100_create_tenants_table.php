<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('central')->create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 100)->unique();
            $table->string('domain', 255)->nullable()->unique();

            // Kredensial koneksi database tenant (per perumahan)
            $table->string('db_driver', 20)->default('mysql');
            $table->string('db_host', 150);
            $table->unsignedInteger('db_port')->default(3306);
            $table->string('db_database', 150);
            $table->string('db_username', 150);
            $table->text('db_password')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('central')->dropIfExists('tenants');
    }
};
