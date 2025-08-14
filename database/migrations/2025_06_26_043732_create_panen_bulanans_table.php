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
        Schema::create('panen_bulanans', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->foreignId('kebun_id')->constrained('kebuns')->onDelete('cascade');
            $table->foreignId('divisi_id')->constrained('divisis')->onDelete('cascade');
            $table->decimal('total_luas_panen', 10, 2)->default(0);
            $table->integer('total_jjg_panen')->default(0);
            $table->decimal('total_timbang_kebun', 10, 2)->default(0);
            $table->decimal('total_timbang_pks', 10, 2)->default(0);
            $table->integer('total_jumlah_tk')->default(0);
            $table->decimal('total_refraksi_kg', 10, 2)->default(0);
            $table->decimal('total_alokasi_budget', 12, 2)->default(0);
            $table->decimal('bjr_bulanan', 10, 2)->default(0); // BJR Bulanan
            $table->decimal('akp_bulanan', 10, 2)->default(0); // AKP Bulanan
            $table->decimal('acv_prod_bulanan', 10, 2)->default(0); // ACV Prod Bulanan
            $table->decimal('refraksi_persen', 10, 2)->default(0); // Refraksi %
            $table->timestamps();
            
            $table->unique(['tahun', 'bulan', 'kebun_id', 'divisi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('panen_bulanans');
    }
};
