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
        Schema::table('panen_harians', function (Blueprint $table) {
            // Change ketrek from VARCHAR to FLOAT and make it nullable
            $table->float('ketrek')->nullable()->change();
            
            // Make other fields nullable to allow empty data
            $table->string('akp_panen', 8)->nullable()->change();
            $table->integer('jumlah_tk_panen')->nullable()->default(0)->change();
            $table->float('luas_panen_ha')->nullable()->default(0)->change();
            $table->integer('jjg_panen_jjg')->nullable()->default(0)->change();
            $table->integer('jjg_kirim_jjg')->nullable()->default(0)->change();
            $table->integer('total_jjg_kirim_jjg')->nullable()->default(0)->change();
            $table->float('tonase_panen_kg')->nullable()->default(0)->change();
            $table->float('refraksi_kg')->nullable()->default(0)->change();
            $table->float('refraksi_persen')->nullable()->default(0)->change();
            $table->integer('restant_jjg')->nullable()->default(0)->change();
            $table->float('bjr_hari_ini')->nullable()->default(0)->change();
            $table->float('output_kg_hk')->nullable()->default(0)->change();
            $table->float('output_ha_hk')->nullable()->default(0)->change();
            $table->float('budget_harian')->nullable()->default(0)->change();
            $table->float('timbang_kebun_harian')->nullable()->default(0)->change();
            $table->float('timbang_pks_harian')->nullable()->default(0)->change();
            $table->float('rotasi_panen')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panen_harians', function (Blueprint $table) {
            // Revert ketrek back to VARCHAR
            $table->string('ketrek', 64)->nullable()->change();
            
            // Revert other fields back to NOT NULL
            $table->integer('jumlah_tk_panen')->default(0)->change();
            $table->float('luas_panen_ha')->default(0)->change();
            $table->integer('jjg_panen_jjg')->default(0)->change();
            $table->integer('jjg_kirim_jjg')->default(0)->change();
            $table->integer('total_jjg_kirim_jjg')->default(0)->change();
            $table->float('tonase_panen_kg')->default(0)->change();
            $table->float('refraksi_kg')->default(0)->change();
            $table->float('refraksi_persen')->default(0)->change();
            $table->integer('restant_jjg')->default(0)->change();
            $table->float('bjr_hari_ini')->default(0)->change();
            $table->float('output_kg_hk')->default(0)->change();
            $table->float('output_ha_hk')->default(0)->change();
            $table->float('budget_harian')->default(0)->change();
            $table->float('timbang_kebun_harian')->default(0)->change();
            $table->float('timbang_pks_harian')->default(0)->change();
            $table->float('rotasi_panen')->default(0)->change();
        });
    }
};
