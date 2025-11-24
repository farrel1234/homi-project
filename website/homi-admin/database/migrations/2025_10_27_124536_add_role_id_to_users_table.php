<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                // sesuaikan tipe ID 'roles' kita: pakai $table->id() => BIGINT unsigned
                $table->foreignId('role_id')
                      ->nullable()
                      ->after('id')                  // taruh setelah PK (opsional)
                      ->constrained('roles')
                      ->nullOnDelete();              // kalau role dihapus → role_id = null
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
                $table->dropColumn('role_id');
            }
        });
    }
};
