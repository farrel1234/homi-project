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
        Schema::table('service_requests', function (Blueprint $table) {
            $table->string('rt_name')->nullable()->after('admin_note');
            $table->string('rt_number')->nullable()->after('rt_name');
            $table->string('rw_number')->nullable()->after('rt_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            $table->dropColumn(['rt_name', 'rt_number', 'rw_number']);
        });
    }
};
