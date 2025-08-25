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
        if (!Schema::hasTable('panen_harians')) {
            return; // nothing to do
        }

        // 1. Tambah kolom baru jika belum ada
        Schema::table('panen_harians', function (Blueprint $table) {
            if (!Schema::hasColumn('panen_harians', 'kebun_id')) {
                $table->unsignedBigInteger('kebun_id')->nullable()->after('kebun');
            }
            if (!Schema::hasColumn('panen_harians', 'divisi_id')) {
                $table->unsignedBigInteger('divisi_id')->nullable()->after('divisi');
            }
        });

        // 2. Update isi kolom baru (loop PHP agar compatible semua DBMS)
        if (Schema::hasTable('kebuns') && Schema::hasTable('divisis')) {
            $kebuns = DB::table('kebuns')->pluck('id', 'nama_kebun');
            $divisis = DB::table('divisis')->pluck('id', 'nama_divisi');
            foreach(DB::table('panen_harians')->select('id','kebun','divisi')->get() as $ph) {
                $kebun_id = $kebuns[$ph->kebun] ?? null;
                $divisi_id = $divisis[$ph->divisi] ?? null;
                DB::table('panen_harians')->where('id', $ph->id)->update([
                    'kebun_id' => $kebun_id,
                    'divisi_id' => $divisi_id,
                ]);
            }
        }

        // 3. (Optional) Tambahkan foreign key constraint jika DBMS support
        $driver = DB::getDriverName();
        if (in_array($driver, ['mysql', 'mariadb', 'pgsql'])) {
            Schema::table('panen_harians', function (Blueprint $table) {
                if (Schema::hasColumn('panen_harians', 'kebun_id') && !self::hasForeign('panen_harians', 'panen_harians_kebun_id_foreign')) {
                    $table->foreign('kebun_id')->references('id')->on('kebuns')->onDelete('cascade');
                }
                if (Schema::hasColumn('panen_harians', 'divisi_id') && !self::hasForeign('panen_harians', 'panen_harians_divisi_id_foreign')) {
                    $table->foreign('divisi_id')->references('id')->on('divisis')->onDelete('cascade');
                }
            });
        }

        // 4. (Optional) Jika ingin mengubah jadi required (TIDAK didukung di SQLite)
        // Kalau perlu, tambah validasi di model/controller, bukan di migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('panen_harians')) return;
        Schema::table('panen_harians', function (Blueprint $table) {
            try { $table->dropForeign(['kebun_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['divisi_id']); } catch (\Throwable $e) {}
            if (Schema::hasColumn('panen_harians', 'kebun_id')) $table->dropColumn('kebun_id');
            if (Schema::hasColumn('panen_harians', 'divisi_id')) $table->dropColumn('divisi_id');
        });
    }

    // Helper to check foreign key existence in a DB-agnostic way
    private static function hasForeign(string $table, string $indexName): bool
    {
        try {
            $connection = Schema::getConnection()->getDoctrineSchemaManager();
            $doctrineTable = $connection->listTableDetails($table);
            return $doctrineTable->hasForeignKey($indexName);
        } catch (\Throwable $e) {
            return false;
        }
    }
};
