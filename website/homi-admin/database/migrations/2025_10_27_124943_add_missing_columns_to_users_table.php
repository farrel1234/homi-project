<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // username
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('id');
                // kalau mau unik dan tabel belum besar, bisa aktifkan:
                // $table->unique('username');
            }

            // password_hash (kita tidak mengganggu kolom 'password' jika ada)
            if (!Schema::hasColumn('users', 'password_hash')) {
                $table->string('password_hash')->nullable()->after('email');
            }

            // full_name
            if (!Schema::hasColumn('users', 'full_name')) {
                $table->string('full_name')->nullable()->after('password_hash');
            }

            // phone
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 30)->nullable()->after('full_name');
            }

            // is_active
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'full_name')) {
                $table->dropColumn('full_name');
            }
            if (Schema::hasColumn('users', 'password_hash')) {
                $table->dropColumn('password_hash');
            }
            if (Schema::hasColumn('users', 'username')) {
                // kalau tadi kamu menambahkan unique index, hapus index dulu sebelum drop column.
                // $table->dropUnique(['username']);
                $table->dropColumn('username');
            }
        });
    }
};
