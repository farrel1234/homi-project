<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');                 // judul pengumuman
            $table->text('content');                 // isi pengumuman
            $table->timestamp('published_at')
                  ->nullable();                      // waktu publish
            $table->foreignId('created_by')          // admin pembuat
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->boolean('is_pinned')->default(false); // untuk "pengumuman penting"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};

