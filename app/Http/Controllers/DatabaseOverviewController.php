<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseOverviewController extends Controller
{
    public function index()
    {
        $driver = DB::getDriverName();
        $tables = [];
        try {
            if ($driver === 'pgsql') {
                $tableNames = collect(DB::select("select tablename from pg_tables where schemaname = coalesce(current_schema(),'public') order by tablename"))
                    ->pluck('tablename');
            } else {
                $tableNames = collect(DB::select("select table_name from information_schema.tables where table_schema not in ('information_schema','pg_catalog')"))
                    ->pluck('table_name');
            }

            foreach ($tableNames as $name) {
                // Skip internal cache tables maybe later if noisy
                $count = 0;
                $columns = [];
                $sample = [];
                try {
                    $count = DB::table($name)->count();
                } catch (\Throwable $e) {}
                try {
                    $columns = Schema::getColumnListing($name);
                } catch (\Throwable $e) {}
                try {
                    $sample = DB::table($name)->limit(3)->get()->map(function($r){ return (array)$r; })->all();
                } catch (\Throwable $e) {}
                $tables[] = [
                    'name' => $name,
                    'count' => $count,
                    'columns' => $columns,
                    'sample' => $sample,
                ];
            }
        } catch (\Throwable $e) {
            return view('db.overview', [
                'error' => $e->getMessage(),
                'driver' => $driver,
                'tables' => []
            ]);
        }

        return view('db.overview', [
            'driver' => $driver,
            'tables' => $tables,
            'error' => null,
        ]);
    }
}
