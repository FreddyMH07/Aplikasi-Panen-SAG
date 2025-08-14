<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing tables if they exist
        Schema::dropIfExists('panen_harians_old');
        Schema::dropIfExists('panen_harians');

        // Create new table with complete structure
        Schema::create('panen_harians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_panen');
            $table->string('bulan', 16);
            $table->integer('tahun');
            $table->string('kebun', 64);
            $table->string('divisi', 64);
            $table->string('akp_panen', 8)->nullable();
            $table->integer('jumlah_tk_panen')->default(0);
            $table->float('luas_panen_ha')->default(0);
            $table->integer('jjg_panen_jjg')->default(0);
            $table->integer('jjg_kirim_jjg')->default(0);
            $table->float('ketrek')->nullable();
            $table->integer('total_jjg_kirim_jjg')->default(0);
            $table->float('tonase_panen_kg')->default(0);
            $table->float('refraksi_kg')->default(0);
            $table->float('refraksi_persen')->default(0);
            $table->integer('restant_jjg')->default(0);
            $table->float('bjr_hari_ini')->default(0);
            $table->float('output_kg_hk')->default(0);
            $table->float('output_ha_hk')->default(0);
            $table->float('budget_harian')->default(0);
            $table->float('timbang_kebun_harian')->default(0);
            $table->float('timbang_pks_harian')->default(0);
            $table->float('rotasi_panen')->default(0);
            $table->string('input_by', 64)->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['tanggal_panen', 'kebun', 'divisi']);
            $table->index(['tahun', 'bulan']);
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
