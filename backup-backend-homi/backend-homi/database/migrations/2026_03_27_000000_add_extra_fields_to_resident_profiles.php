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
        Schema::table('resident_profiles', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('user_id');
            $table->string('pekerjaan')->nullable()->after('alamat');
            $table->string('tempat_tanggal_lahir')->nullable()->after('pekerjaan');
            $table->string('jenis_kelamin', 20)->nullable()->after('tempat_tanggal_lahir');
            
            // Tambahan RT/RW jika belum ada (antisipasi admin detail)
            if (!Schema::hasColumn('resident_profiles', 'rt')) {
                $table->string('rt', 10)->nullable()->after('no_rumah');
            }
            if (!Schema::hasColumn('resident_profiles', 'rw')) {
                $table->string('rw', 10)->nullable()->after('rt');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_profiles', function (Blueprint $table) {
            $table->dropColumn(['nik', 'pekerjaan', 'tempat_tanggal_lahir', 'jenis_kelamin', 'rt', 'rw']);
        });
    }
};
