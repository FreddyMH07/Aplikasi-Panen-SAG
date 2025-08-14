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
        // 1. Tambah kolom baru
        Schema::table('panen_harians', function (Blueprint $table) {
            $table->unsignedBigInteger('kebun_id')->nullable()->after('kebun');
            $table->unsignedBigInteger('divisi_id')->nullable()->after('divisi');
        });

        // 2. Update isi kolom baru (loop PHP agar compatible semua DBMS)
        $kebuns = DB::table('kebuns')->pluck('id', 'nama_kebun');
        $divisis = DB::table('divisis')->pluck('id', 'nama_divisi');
        foreach(DB::table('panen_harians')->get() as $ph) {
            $kebun_id = $kebuns[$ph->kebun] ?? null;
            $divisi_id = $divisis[$ph->divisi] ?? null;
            DB::table('panen_harians')->where('id', $ph->id)->update([
                'kebun_id' => $kebun_id,
                'divisi_id' => $divisi_id,
            ]);
        }

        // 3. (Optional) Tambahkan foreign key constraint jika DBMS support
        Schema::table('panen_harians', function (Blueprint $table) {
            // Hati-hati: Foreign key hanya work di SQLite jika support dan enabled
            $table->foreign('kebun_id')->references('id')->on('kebuns')->onDelete('cascade');
            $table->foreign('divisi_id')->references('id')->on('divisis')->onDelete('cascade');
        });

        // 4. (Optional) Jika ingin mengubah jadi required (TIDAK didukung di SQLite)
        // Kalau perlu, tambah validasi di model/controller, bukan di migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panen_harians', function (Blueprint $table) {
            $table->dropForeign(['kebun_id']);
            $table->dropForeign(['divisi_id']);
            $table->dropColumn(['kebun_id', 'divisi_id']);
        });
    }
};
