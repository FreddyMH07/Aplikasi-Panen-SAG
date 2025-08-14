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
        Schema::create('master_data', function (Blueprint $table) {
            $table->id();
            $table->string('kebun', 64);
            $table->string('divisi', 64);
            $table->float('sph_panen')->default(136);
            $table->float('luas_tm')->default(0); // Luas Tanaman Menghasilkan
            $table->float('budget_alokasi')->default(0);
            $table->integer('pkk')->default(0); // Pokok Kelapa Sawit
            $table->string('bulan', 16);
            $table->integer('tahun');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['kebun', 'divisi', 'tahun', 'bulan']);
            $table->unique(['kebun', 'divisi', 'tahun', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_data');
    }
};
