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
            $table->string('nama_rt')->nullable()->after('alamat');
            $table->string('no_rt')->nullable()->after('nama_rt');
            $table->string('no_rw')->nullable()->after('no_rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_profiles', function (Blueprint $table) {
            $table->dropColumn(['nama_rt', 'no_rt', 'no_rw']);
        });
    }
};
