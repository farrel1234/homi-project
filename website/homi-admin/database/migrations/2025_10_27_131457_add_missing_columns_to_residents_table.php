<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Pastikan tabel residents memang ada
        if (!Schema::hasTable('residents')) {
            Schema::create('residents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('house_number')->nullable();
                $table->text('address')->nullable();
                $table->string('id_number')->nullable();
                $table->string('family_head')->nullable();
                $table->text('other_info')->nullable();
                $table->timestamps();
            });
            return;
        }

        Schema::table('residents', function (Blueprint $table) {
            // Tambah kolom user_id kalau belum ada
            if (!Schema::hasColumn('residents', 'user_id')) {
                $table->foreignId('user_id')->unique()->after('id')->constrained('users')->cascadeOnDelete();
            }

            if (!Schema::hasColumn('residents', 'house_number')) {
                $table->string('house_number')->nullable();
            }
            if (!Schema::hasColumn('residents', 'address')) {
                $table->text('address')->nullable();
            }
            if (!Schema::hasColumn('residents', 'id_number')) {
                $table->string('id_number')->nullable();
            }
            if (!Schema::hasColumn('residents', 'family_head')) {
                $table->string('family_head')->nullable();
            }
            if (!Schema::hasColumn('residents', 'other_info')) {
                $table->text('other_info')->nullable();
            }

            // Tambah timestamps jika belum ada
            if (!Schema::hasColumn('residents', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('residents', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Turunin perubahan secara aman (hapus FK + kolom tambahan)
        if (Schema::hasTable('residents')) {
            Schema::table('residents', function (Blueprint $table) {
                if (Schema::hasColumn('residents', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                if (Schema::hasColumn('residents', 'house_number')) $table->dropColumn('house_number');
                if (Schema::hasColumn('residents', 'address')) $table->dropColumn('address');
                if (Schema::hasColumn('residents', 'id_number')) $table->dropColumn('id_number');
                if (Schema::hasColumn('residents', 'family_head')) $table->dropColumn('family_head');
                if (Schema::hasColumn('residents', 'other_info')) $table->dropColumn('other_info');
                if (Schema::hasColumn('residents', 'created_at')) $table->dropColumn('created_at');
                if (Schema::hasColumn('residents', 'updated_at')) $table->dropColumn('updated_at');
            });
        }
    }
};
