<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('panen_harians')) return;
        Schema::table('panen_harians', function (Blueprint $table) {
            // Add unique constraint to prevent duplicates on same date/kebun/divisi
            $table->unique(['tanggal_panen', 'kebun', 'divisi'], 'panen_harians_unique_tanggal_kebun_divisi');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('panen_harians')) return;
        Schema::table('panen_harians', function (Blueprint $table) {
            $table->dropUnique('panen_harians_unique_tanggal_kebun_divisi');
        });
    }
};
