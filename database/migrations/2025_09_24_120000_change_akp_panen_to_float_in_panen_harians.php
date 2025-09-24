<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            // Convert akp_panen from varchar to double precision
            // First, normalize empty strings to NULL to avoid cast issues
            try { DB::statement("UPDATE panen_harians SET akp_panen = NULL WHERE akp_panen = ''"); } catch (\Throwable $e) {}
            try { DB::statement("ALTER TABLE panen_harians ALTER COLUMN akp_panen TYPE double precision USING (NULLIF(akp_panen, '')::double precision)"); } catch (\Throwable $e) {}
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasColumn('panen_harians', 'akp_panen')) {
                Schema::table('panen_harians', function (Blueprint $table) {
                    $table->float('akp_panen')->nullable()->change();
                });
            }
        } else {
            // For sqlite or others, try a best-effort change when supported via DBAL
            if (class_exists(\Doctrine\DBAL\DriverManager::class) && Schema::hasColumn('panen_harians', 'akp_panen')) {
                Schema::table('panen_harians', function (Blueprint $table) {
                    $table->float('akp_panen')->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            try { DB::statement("ALTER TABLE panen_harians ALTER COLUMN akp_panen TYPE varchar(8)"); } catch (\Throwable $e) {}
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasColumn('panen_harians', 'akp_panen')) {
                Schema::table('panen_harians', function (Blueprint $table) {
                    $table->string('akp_panen', 8)->nullable()->change();
                });
            }
        }
    }
};
