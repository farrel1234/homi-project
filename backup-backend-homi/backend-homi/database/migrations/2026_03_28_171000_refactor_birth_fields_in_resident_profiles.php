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
            $table->string('tempat_lahir')->nullable()->after('pekerjaan');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            
            // Note: We'll keep tempat_tanggal_lahir for now if it exists, or drop it.
            // User requested to split it.
            if (Schema::hasColumn('resident_profiles', 'tempat_tanggal_lahir')) {
                // $table->dropColumn('tempat_tanggal_lahir'); 
                // Don't drop yet in case we want to migrate data. 
                // But for a clean dev, we'll drop it in a separate or here.
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resident_profiles', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'tanggal_lahir']);
        });
    }
};
