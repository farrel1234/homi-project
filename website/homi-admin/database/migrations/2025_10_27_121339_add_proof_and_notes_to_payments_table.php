<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'proof_path')) {
                $table->string('proof_path')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable()->after('proof_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('payments', 'proof_path')) {
                $table->dropColumn('proof_path');
            }
        });
    }
};
