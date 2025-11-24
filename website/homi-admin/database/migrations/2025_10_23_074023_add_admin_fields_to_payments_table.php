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
    Schema::table('payments', function (Blueprint $table) {
        $table->string('method')->nullable()->after('amount');        // transfer/QRIS/tunai
        $table->string('proof_path')->nullable()->after('method');    // path file bukti
        $table->text('admin_note')->nullable()->after('proof_path');  // alasan admin
        $table->string('verified_by')->nullable()->after('admin_note'); // nama/id admin
        $table->dateTime('paid_at')->nullable()->after('verified_by'); // waktu valid
    });
}

    public function down(): void
{
    Schema::table('payments', function (Blueprint $table) {
        $table->dropColumn(['method','proof_path','admin_note','verified_by','paid_at']);
    });
}
};
