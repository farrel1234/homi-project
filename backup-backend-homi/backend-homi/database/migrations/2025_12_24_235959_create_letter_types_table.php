<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('letter_types')) {
            return;
        }

        Schema::create('letter_types', function (Blueprint $table) {
            // int unsigned agar cocok dengan FK migration request_types
            $table->increments('id');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->longText('template_html')->nullable();
            $table->json('required_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_types');
    }
};
