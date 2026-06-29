<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::connection('central')->hasTable('tenants')) {
            Schema::connection('central')->table('tenants', function (Blueprint $table) {
                if (!Schema::connection('central')->hasColumn('tenants', 'plan')) {
                    $table->string('plan', 50)->default('trial')->after('is_active');
                }
                if (!Schema::connection('central')->hasColumn('tenants', 'trial_ends_at')) {
                    $table->timestamp('trial_ends_at')->nullable()->after('plan');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::connection('central')->hasTable('tenants')) {
            Schema::connection('central')->table('tenants', function (Blueprint $table) {
                $table->dropColumn(['plan', 'trial_ends_at']);
            });
        }
    }
};
