<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Tambah kolom kalau belum ada
        if (!Schema::hasColumn('request_types', 'letter_type_id')) {
            Schema::table('request_types', function (Blueprint $table) {
                // INT UNSIGNED karena letter_types.id kamu INT UNSIGNED
                $table->unsignedInteger('letter_type_id')->nullable()->after('name');
            });
        }

        // 2) Paksa tipenya jadi INT UNSIGNED (jaga-jaga kalau kolom keburu kebentuk BIGINT)
        try {
            DB::statement('ALTER TABLE request_types MODIFY letter_type_id INT UNSIGNED NULL');
        } catch (\Throwable $e) {
            // ignore
        }

        // 3) Drop FK kalau sempat kebentuk / nyangkut
        try {
            DB::statement('ALTER TABLE request_types DROP FOREIGN KEY request_types_letter_type_id_foreign');
        } catch (\Throwable $e) {
            // ignore
        }

        // 4) Tambah foreign key
        Schema::table('request_types', function (Blueprint $table) {
            $table->foreign('letter_type_id', 'request_types_letter_type_id_foreign')
                ->references('id')
                ->on('letter_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE request_types DROP FOREIGN KEY request_types_letter_type_id_foreign');
        } catch (\Throwable $e) {
            // ignore
        }

        if (Schema::hasColumn('request_types', 'letter_type_id')) {
            Schema::table('request_types', function (Blueprint $table) {
                $table->dropColumn('letter_type_id');
            });
        }
    }
};
