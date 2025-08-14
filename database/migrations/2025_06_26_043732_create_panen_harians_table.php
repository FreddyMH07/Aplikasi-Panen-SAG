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
        Schema::create('panen_harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_panen');
            $table->foreignId('kebun_id')->constrained('kebuns')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->decimal('luas_panen', 10, 2)->default(0);
            $table->integer('jjg_panen')->default(0); // Jumlah Janjang
            $table->decimal('timbang_kebun', 10, 2)->default(0); // Timbangan di kebun (kg)
            $table->decimal('timbang_pks', 10, 2)->default(0); // Timbangan di PKS (kg)
            $table->integer('jumlah_tk')->default(0); // Jumlah Tenaga Kerja
            $table->decimal('refraksi_kg', 10, 2)->default(0);
            $table->decimal('alokasi_budget', 12, 2)->default(0);
            $table->json('additional_data')->nullable(); // Untuk kolom dinamis
            $table->timestamps();
            
            $table->index(['tanggal_panen', 'kebun_id', 'divisi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panen_harians');
    }
};
