<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('service_requests', 'data_input')) {
                $table->json('data_input')->nullable()->after('subject');
            }
            if (!Schema::hasColumn('service_requests', 'pdf_path')) {
                $table->string('pdf_path', 255)->nullable()->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            if (Schema::hasColumn('service_requests', 'data_input')) {
                $table->dropColumn('data_input');
            }
            if (Schema::hasColumn('service_requests', 'pdf_path')) {
                $table->dropColumn('pdf_path');
            }
        });
    }
};
