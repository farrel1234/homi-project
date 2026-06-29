<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('fee_invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('fee_type_id')->constrained('fee_types')->restrictOnDelete();

            // pakai tanggal awal bulan: 2025-08-01
            $table->date('period');

            $table->unsignedInteger('amount');

            $table->string('status')->default('unpaid'); // unpaid|pending|paid|rejected
            $table->string('trx_id')->nullable();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->unique(['user_id','fee_type_id','period']); // 1 jenis iuran per bulan per warga
            $table->index(['status', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_invoices');
    }
};
