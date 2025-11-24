<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('payments')) return;

        // 1) Ubah tipe jadi string(20) + default 'pending'
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'status')) {
                $table->string('status', 20)->default('pending')->change();
            } else {
                // kalau belum ada, tambahkan sekalian
                $table->string('status', 20)->default('pending')->after('paid_at');
            }
        });

        // 2) (Opsional) Normalisasi nilai lama ke salah satu dari 4 status
        //    Map kasar dari yang umum dipakai di Indonesia
        DB::table('payments')->whereIn('status', ['Menunggu','menunggu','pending_payment'])->update(['status' => 'pending']);
        DB::table('payments')->whereIn('status', ['Lunas','lunas','success','paid_payment'])->update(['status' => 'paid']);
        DB::table('payments')->whereIn('status', ['Gagal','gagal','failed_payment','ditolak'])->update(['status' => 'failed']);
        DB::table('payments')->whereIn('status', ['Dibatalkan','dibatalkan','cancel'])->update(['status' => 'cancelled']);

        // 3) Pastikan nilai kosong/null diisi 'pending'
        DB::table('payments')->whereNull('status')->update(['status' => 'pending']);
        DB::table('payments')->where('status','')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        // Tidak mengembalikan ke ENUM untuk menjaga kestabilan.
        // Jika perlu, sesuaikan manual dengan tipe awal.
    }
};
