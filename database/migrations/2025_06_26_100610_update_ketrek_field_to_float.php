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
        // Change ketrek from string to float, in a connection-safe way
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Postgres: use explicit USING clause and safe checks
            DB::unprepared(<<<'SQL'
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name='panen_harians' AND column_name='ketrek' 
          AND data_type IN ('character varying','text')
    ) THEN
        -- Empty string -> NULL then cast
        EXECUTE 'UPDATE panen_harians SET ketrek = NULL WHERE ketrek = '''''';';
        EXECUTE 'ALTER TABLE panen_harians ALTER COLUMN ketrek TYPE double precision USING (ketrek::double precision)';
    END IF;
END $$;
SQL);
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            // MySQL/MariaDB: attempt a direct type change if column exists
            if (Schema::hasColumn('panen_harians', 'ketrek')) {
                Schema::table('panen_harians', function (Blueprint $table) {
                    $table->float('ketrek')->nullable()->change();
                });
            }
        } else {
            // SQLite or other drivers: skip type conversion (SQLite doesn't support ALTER TYPE easily)
            // Newer migrations already recreate this column as float.
        }

        // Make other fields nullable to allow empty data (only when DBAL/change() is available)
        $canChange = class_exists(\Doctrine\DBAL\DriverManager::class) && !($driver === 'sqlite');
        if ($canChange) {
            Schema::table('panen_harians', function (Blueprint $table) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::unprepared(<<<'SQL'
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns 
        WHERE table_name='panen_harians' AND column_name='ketrek' 
          AND data_type = 'double precision'
    ) THEN
        EXECUTE 'ALTER TABLE panen_harians ALTER COLUMN ketrek TYPE varchar(64) USING (ketrek::varchar)';
    END IF;
END $$;
SQL);
        } elseif (in_array($driver, ['mysql', 'mariadb'])) {
            if (Schema::hasColumn('panen_harians', 'ketrek')) {
                Schema::table('panen_harians', function (Blueprint $table) {
                    $table->string('ketrek', 64)->nullable()->change();
                });
            }
        }

        $canChange = class_exists(\Doctrine\DBAL\DriverManager::class) && !($driver === 'sqlite');
        if ($canChange) {
            Schema::table('panen_harians', function (Blueprint $table) {
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
    }
};
