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
        Schema::table('fee_types', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_types', 'amount')) {
                $table->decimal('amount', 15, 2)->default(0)->after('name');
            }
            if (!Schema::hasColumn('fee_types', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('amount');
            }
            if (!Schema::hasColumn('fee_types', 'description')) {
                $table->text('description')->nullable()->after('is_recurring');
            }
        });

        Schema::table('request_types', function (Blueprint $table) {
            if (!Schema::hasColumn('request_types', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('request_types', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('content');
            }
            if (!Schema::hasColumn('announcements', 'category')) {
                $table->string('category')->nullable()->after('is_public');
            }
            if (!Schema::hasColumn('announcements', 'start_at')) {
                $table->timestamp('start_at')->nullable()->after('category');
            }
            if (!Schema::hasColumn('announcements', 'end_at')) {
                $table->timestamp('end_at')->nullable()->after('start_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('fee_types', function (Blueprint $table) {
            $table->dropColumn(['amount', 'is_recurring', 'description']);
        });

        Schema::table('request_types', function (Blueprint $table) {
            $table->dropColumn(['description', 'icon']);
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['is_public', 'category', 'start_at', 'end_at']);
        });
    }
};
