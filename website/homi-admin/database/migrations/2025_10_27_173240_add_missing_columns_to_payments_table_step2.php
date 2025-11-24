<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Jika table 'payments' belum ada sama sekali, buat sekalian lengkap
        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('currency', 10)->default('IDR');
                $table->text('description')->nullable();
                $table->date('due_date')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->string('status', 20)->default('pending'); // pending|paid|failed|cancelled
                $table->string('payment_method', 50)->nullable();
                $table->string('payment_reference', 100)->nullable();
                $table->string('proof_path')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            return;
        }

        // Kalau sudah ada, tambahkan kolom yang kurang saja (aman untuk schema yang beda)
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->foreignId('user_id')->after('id')->constrained('users')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('user_id');
            }
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default('IDR')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'description')) {
                $table->text('description')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('payments', 'due_date')) {
                $table->date('due_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('payments', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('due_date');
            }
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status', 20)->default('pending')->after('paid_at');
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 50)->nullable()->after('status');
            }
            if (!Schema::hasColumn('payments', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('payments', 'proof_path')) {
                $table->string('proof_path')->nullable()->after('payment_reference');
            }
            if (!Schema::hasColumn('payments', 'notes')) {
                $table->text('notes')->nullable()->after('proof_path');
            }
            if (!Schema::hasColumn('payments', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('payments', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('payments')) return;

        Schema::table('payments', function (Blueprint $table) {
            // Turunkan perubahan dengan aman
            foreach (['notes','proof_path','payment_reference','payment_method','status','paid_at','due_date','description','currency','amount'] as $col) {
                if (Schema::hasColumn('payments', $col)) $table->dropColumn($col);
            }
            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            if (Schema::hasColumn('payments', 'created_at')) $table->dropColumn('created_at');
            if (Schema::hasColumn('payments', 'updated_at')) $table->dropColumn('updated_at');
        });
    }
};
