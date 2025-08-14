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
        Schema::create('kebuns', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kebun');
            $table->string('kode_kebun')->unique();
            $table->text('alamat')->nullable();
            $table->decimal('luas_total', 10, 2)->nullable();
            $table->integer('sph_panen')->default(136); // Standar Pokok per Hektar
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kebuns');
    }
};
