<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->date('tanggal_pengaduan');
            $table->string('tempat_kejadian');
            $table->string('perihal');
            // path file foto/gambar yang diupload
            $table->string('foto_path')->nullable();
            // optional: kalau mau simpan status
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
