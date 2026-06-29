<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('request_type_id')->constrained('request_types');

            $table->string('reporter_name');     // nama pelapor (boleh beda dari user.name)
            $table->date('request_date');        // tanggal pengajuan
            $table->string('place');             // tempat
            $table->string('subject');           // perihal

            // status verifikasi / proses
            $table->enum('status', ['submitted','processed','approved','rejected'])->default('submitted');

            $table->text('admin_note')->nullable();          // catatan admin (alasan / info)
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['request_type_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
