<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PanenHarianController;
use App\Http\Controllers\PanenBulananController;
use App\Http\Controllers\KebunController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\DatabaseOverviewController;
use App\Http\Controllers\TableColumnController;

// Authentication Routes
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
// Simple health check (no auth) for Railway
Route::get('/health', function() { return response()->json(['status' => 'ok']); });
// Lightweight diagnostics (no auth): DB connectivity and schema snapshot
Route::get('/diag', function(\Illuminate\Http\Request $request) {
    $out = [
        'app_env' => config('app.env'),
        'php_version' => PHP_VERSION,
        'app_url' => config('app.url'),
        'driver' => null,
        'database' => null,
        'db_ok' => false,
        'pgsql_resolved' => null,
        'tables' => [],
        'request' => [
            'full_url' => $request->fullUrl(),
            'scheme' => $request->getScheme(),
            'is_secure' => $request->isSecure(),
            'host' => $request->getHost(),
            'forwarded_proto' => $request->headers->get('X-Forwarded-Proto'),
            'forwarded_host' => $request->headers->get('X-Forwarded-Host'),
            'forwarded_port' => $request->headers->get('X-Forwarded-Port'),
            'forwarded_for' => $request->headers->get('X-Forwarded-For'),
            'trusted_proxies_env' => env('TRUSTED_PROXIES'),
        ],
        'panen_harians' => [
            'exists' => false,
            'ketrek' => [
                'exists' => false,
                'data_type' => null,
            ],
        ],
        'error' => null,
    ];
    try {
        $out['driver'] = DB::getDriverName();
        if ($out['driver'] === 'pgsql') {
            $out['pgsql_resolved'] = [
                'host' => config('database.connections.pgsql.host'),
                'port' => config('database.connections.pgsql.port'),
                'database' => config('database.connections.pgsql.database'),
                'username' => config('database.connections.pgsql.username'),
                'sslmode' => config('database.connections.pgsql.sslmode'),
                'search_path' => config('database.connections.pgsql.search_path'),
            ];
        }
        if ($out['driver'] === 'pgsql') {
            $out['database'] = optional(DB::selectOne('select current_database() as db'))?->db;
        }
        if ($out['driver'] === 'mysql' || $out['driver'] === 'mariadb') {
            $out['database'] = optional(DB::selectOne('select database() as db'))?->db;
        }
        DB::select('select 1');
        $out['db_ok'] = true;

        // Tables
        if ($out['driver'] === 'pgsql') {
            $tables = collect(DB::select("select tablename from pg_tables where schemaname = coalesce(current_schema(),'public') order by tablename"))
                ->map(fn($r) => $r->tablename)->all();
        } else {
            $tables = collect(DB::select('select * from information_schema.tables'))
                ->map(function($r){ return $r->table_name ?? $r->TABLE_NAME ?? null; })
                ->filter()->unique()->values()->all();
        }
        $out['tables'] = $tables;

        // panen_harians + ketrek column type
        if (in_array('panen_harians', $tables)) {
            $out['panen_harians']['exists'] = true;
            if ($out['driver'] === 'pgsql') {
                $col = DB::table('information_schema.columns')
                    ->select('data_type')
                    ->where('table_name','panen_harians')
                    ->where('column_name','ketrek')
                    ->first();
                if ($col) {
                    $out['panen_harians']['ketrek']['exists'] = true;
                    $out['panen_harians']['ketrek']['data_type'] = $col->data_type;
                }
            } else {
                try {
                    $exists = Schema::hasColumn('panen_harians','ketrek');
                    $out['panen_harians']['ketrek']['exists'] = $exists;
                } catch (\Throwable $e) {}
            }
        }
    } catch (\Throwable $e) {
        $out['error'] = $e->getMessage();
    }
    return response()->json($out);
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Debug JSON endpoint for dashboard data (auth-protected)
    Route::get('/dashboard.json', function(\Illuminate\Http\Request $request) {
        // Delegate to controller to compute the same payload as view
        /** @var \App\Http\Controllers\DashboardController $ctrl */
        $ctrl = app(\App\Http\Controllers\DashboardController::class);
        try {
            // Manually run index() up to view data construction
            // Replicate controller logic by invoking index and intercepting variables via view()->getData() is not trivial
            // Instead, call controller methods directly
            $refMethod = new \ReflectionMethod($ctrl, 'index');
            // Fallback: recompute by calling private helpers via exposing minimal public proxy
        } catch (\Throwable $e) {
            // If reflection fails due to visibility, fallback to re-running core logic here
        }

        // Rebuild core computations inline (mirroring controller index)
        $today = \Carbon\Carbon::today();
        $selectedMonthParam = $request->get('bulan');
        $selectedYearParam = $request->get('tahun');
        $monthToNumber = function(string $bulan){
            $map = ['JANUARI'=>1,'FEBRUARI'=>2,'MARET'=>3,'APRIL'=>4,'MEI'=>5,'JUNI'=>6,'JULI'=>7,'AGUSTUS'=>8,'SEPTEMBER'=>9,'OKTOBER'=>10,'NOVEMBER'=>11,'DESEMBER'=>12,'JANUARY'=>1,'FEBRUARY'=>2,'MARCH'=>3,'APRIL'=>4,'MAY'=>5,'JUNE'=>6,'JULY'=>7,'AUGUST'=>8,'SEPTEMBER'=>9,'OCTOBER'=>10,'NOVEMBER'=>11,'DECEMBER'=>12];
            $key = strtoupper(trim($bulan));
            return $map[$key] ?? \Carbon\Carbon::now()->month;
        };
        $currentMonth = $selectedMonthParam ? $monthToNumber($selectedMonthParam) : \Carbon\Carbon::now()->month;
        $currentYear = $selectedYearParam ? (int)$selectedYearParam : \Carbon\Carbon::now()->year;
        $monthStart = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth();

        $kebunList = \App\Models\MasterData::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        $divisiList = $request->filled('kebun')
            ? \App\Models\MasterData::where('kebun', $request->kebun)->select('divisi')->distinct()->orderBy('divisi')->pluck('divisi')
            : \App\Models\MasterData::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');

        $query = \App\Models\PanenHarian::whereDate('tanggal_panen', $today);
        if ($request->filled('kebun')) { $query->where('kebun', $request->kebun); }
        if ($request->filled('divisi')) { $query->where('divisi', $request->divisi); }
        $hasToday = (clone $query)->exists();
        if (!$hasToday) {
            $fallbackDateQuery = \App\Models\PanenHarian::whereYear('tanggal_panen', $currentYear)->whereMonth('tanggal_panen', $currentMonth);
            if ($request->filled('kebun')) { $fallbackDateQuery->where('kebun', $request->kebun); }
            if ($request->filled('divisi')) { $fallbackDateQuery->where('divisi', $request->divisi); }
            $fallbackDate = $fallbackDateQuery->orderBy('tanggal_panen', 'desc')->value('tanggal_panen');
            if ($fallbackDate) {
                $query = \App\Models\PanenHarian::whereDate('tanggal_panen', $fallbackDate);
                if ($request->filled('kebun')) { $query->where('kebun', $request->kebun); }
                if ($request->filled('divisi')) { $query->where('divisi', $request->divisi); }
            }
        }
        $todayData = $query->selectRaw('COALESCE(SUM(luas_panen_ha),0) as total_luas, COALESCE(SUM(jjg_panen_jjg),0) as total_jjg, COALESCE(SUM(timbang_kebun_harian),0) as total_timbang_kebun, COALESCE(SUM(timbang_pks_harian),0) as total_timbang_pks, COALESCE(SUM(jumlah_tk_panen),0) as total_tk, COALESCE(SUM(refraksi_kg),0) as total_refraksi, COALESCE(SUM(restant_jjg),0) as total_restan_jjg, COALESCE(SUM(budget_harian),0) as total_budget, COALESCE(SUM(tonase_panen_kg),0) as total_tonase')->first();

        $monthlyQuery = \App\Models\PanenHarian::whereBetween('tanggal_panen', [$monthStart, $monthEnd]);
        if ($request->filled('kebun')) { $monthlyQuery->where('kebun', $request->kebun); }
        if ($request->filled('divisi')) { $monthlyQuery->where('divisi', $request->divisi); }
        $monthlyData = $monthlyQuery->selectRaw('COALESCE(SUM(luas_panen_ha),0) as total_luas, COALESCE(SUM(jjg_panen_jjg),0) as total_jjg, COALESCE(SUM(timbang_kebun_harian),0) as total_timbang_kebun, COALESCE(SUM(timbang_pks_harian),0) as total_timbang_pks, COALESCE(SUM(jumlah_tk_panen),0) as total_tk, COALESCE(SUM(refraksi_kg),0) as total_refraksi, COALESCE(SUM(restant_jjg),0) as total_restan_jjg, COALESCE(SUM(budget_harian),0) as total_budget, COALESCE(SUM(tonase_panen_kg),0) as total_tonase')->first();

        $calc = function($data) {
            if (!$data) return ['bjr'=>0,'akp'=>0,'acv_prod'=>0,'selisih'=>0,'selisih_persen'=>0,'refraksi_persen'=>0,'refraksi_kg'=>0,'restan_jjg'=>0,'restan_persen'=>0,'total_produksi'=>0,'total_tk'=>0];
            $bjr = $data->total_jjg > 0 ? round($data->total_timbang_kebun / $data->total_jjg, 2) : 0;
            $akp = ($data->total_luas * 136) > 0 ? round($data->total_jjg / ($data->total_luas * 136), 4) : 0;
            $acv_prod = $data->total_budget > 0 ? round(100 * $data->total_timbang_pks / $data->total_budget, 2) : 0;
            $selisih = round($data->total_timbang_pks - $data->total_timbang_kebun, 2);
            $refraksi_persen = $data->total_tonase > 0 ? round(100 * ($data->total_refraksi ?? 0) / $data->total_tonase, 2) : 0;
            $selisih_persen = $data->total_timbang_pks > 0 ? round(100 * ($data->total_timbang_pks - $data->total_timbang_kebun) / $data->total_timbang_pks, 2) : 0;
            $restan_jjg = (int)($data->total_restan_jjg ?? 0);
            $restan_persen = ($data->total_jjg ?? 0) > 0 ? round(100 * $restan_jjg / $data->total_jjg, 2) : 0;
            return ['bjr'=>$bjr,'akp'=>$akp,'acv_prod'=>$acv_prod,'selisih'=>$selisih,'selisih_persen'=>$selisih_persen,'refraksi_persen'=>$refraksi_persen,'refraksi_kg'=>round(($data->total_refraksi ?? 0), 2),'restan_jjg'=>$restan_jjg,'restan_persen'=>$restan_persen,'total_produksi'=>round($data->total_timbang_pks,2),'total_tk'=>$data->total_tk ?? 0];
        };
        $todayMetrics = $calc($todayData);
        $monthlyMetrics = $calc($monthlyData);

        $totalHk = (float)($monthlyData->total_tk ?? 0);
        $monthlyMetrics['ha_per_hk'] = $totalHk > 0 ? round(($monthlyData->total_luas ?? 0) / $totalHk, 2) : 0;
        $monthlyMetrics['ton_per_hk'] = $totalHk > 0 ? round(($monthlyData->total_timbang_pks ?? 0) / $totalHk, 2) : 0;

        $monthNameEnglish = \Carbon\Carbon::create($currentYear, $currentMonth, 1)->format('F');
        $pkkQuery = \App\Models\MasterData::where('tahun', $currentYear)->where('bulan', $monthNameEnglish);
        if ($request->filled('kebun')) { $pkkQuery->where('kebun', $request->kebun); }
        if ($request->filled('divisi')) { $pkkQuery->where('divisi', $request->divisi); }
        $totalPkk = (int)$pkkQuery->sum('pkk');
        $monthlyMetrics['jjg_per_pkk'] = ($totalPkk > 0) ? round(($monthlyData->total_jjg ?? 0) / $totalPkk, 2) : 0;
        $monthlyMetrics['total_pkk'] = $totalPkk;

        // Chart data (reuse controller method)
        $chartData = app(\App\Http\Controllers\DashboardController::class)->getChartData($request);

        $indoMonths = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
        $summaryTitle = 'Ringkasan Bulan ' . ($indoMonths[$currentMonth] ?? date('F')) . ' ' . $currentYear;
        $selectedFilters = ['kebun'=>$request->get('kebun'),'divisi'=>$request->get('divisi'),'bulan'=>$selectedMonthParam,'tahun'=>$selectedYearParam];

        return response()->json(compact('todayMetrics','monthlyMetrics','chartData','kebunList','divisiList','summaryTitle','selectedFilters'));
    })->name('dashboard.json');
    
    // Panen Harian Routes
    Route::prefix('panen-harian')->name('panen-harian.')->group(function () {
        Route::get('/', [PanenHarianController::class, 'index'])->name('index');
        Route::get('/create', [PanenHarianController::class, 'create'])->name('create');
        Route::post('/', [PanenHarianController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PanenHarianController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PanenHarianController::class, 'update'])->name('update');
        Route::delete('/{id}', [PanenHarianController::class, 'destroy'])->name('destroy');
        Route::get('/export', [PanenHarianController::class, 'export'])->name('export');
        Route::post('/import', [PanenHarianController::class, 'import'])->name('import');
        Route::get('/data', [PanenHarianController::class, 'getData'])->name('data');
    });
    
    // Panen Bulanan Routes
    Route::prefix('panen-bulanan')->name('panen-bulanan.')->group(function () {
        Route::get('/', [PanenBulananController::class, 'index'])->name('index');
        Route::get('/data', [PanenBulananController::class, 'getData'])->name('data');
        Route::get('/export', [PanenBulananController::class, 'export'])->name('export');
        Route::post('/generate', [PanenBulananController::class, 'generate'])->name('generate');
    });
    
    // Master Data Routes
    Route::prefix('master')->name('master.')->group(function () {
        // Kebun (Legacy - untuk kompatibilitas)
        Route::resource('kebun', KebunController::class);
        
        // Divisi (Legacy - untuk kompatibilitas)
        Route::resource('divisi', DivisiController::class);
        
        // Master Data (New comprehensive master data)
        Route::prefix('master-data')->name('master-data.')->group(function () {
            Route::get('/', [MasterDataController::class, 'index'])->name('index');
            Route::get('/create', [MasterDataController::class, 'create'])->name('create');
            Route::post('/', [MasterDataController::class, 'store'])->name('store');
            Route::get('/{id}', [MasterDataController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [MasterDataController::class, 'edit'])->name('edit');
            Route::put('/{id}', [MasterDataController::class, 'update'])->name('update');
            Route::delete('/{id}', [MasterDataController::class, 'destroy'])->name('destroy');
            Route::get('/data/table', [MasterDataController::class, 'getData'])->name('data');
            Route::get('/export/excel', [MasterDataController::class, 'export'])->name('export');
            Route::post('/import/excel', [MasterDataController::class, 'import'])->name('import');
        });
    });
    
    // Database Overview Route
    Route::get('/db/overview', [DatabaseOverviewController::class, 'index'])->name('db.overview');

    // Table Columns Management Routes
    Route::prefix('table-columns')->name('table-columns.')->group(function () {
        Route::get('/', [TableColumnController::class, 'index'])->name('index');
        Route::get('/data', [TableColumnController::class, 'getData'])->name('data');
        Route::get('/create', [TableColumnController::class, 'create'])->name('create');
        Route::post('/', [TableColumnController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TableColumnController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TableColumnController::class, 'update'])->name('update');
        Route::delete('/{id}', [TableColumnController::class, 'destroy'])->name('destroy');
    });
    
    // API Routes for AJAX
    Route::prefix('api')->name('api.')->group(function () {
        // Legacy routes (untuk kompatibilitas)
        Route::get('/divisi-by-kebun/{kebun_id}', function($kebun_id) {
            return \App\Models\Divisi::where('kebun_id', $kebun_id)->where('is_active', true)->get();
        })->name('divisi-by-kebun');
        
        // New API routes untuk struktur baru
        Route::get('/divisi-by-kebun-name/{kebun}', [PanenHarianController::class, 'getDivisiByKebun'])
            ->name('divisi-by-kebun-name');
        
        Route::get('/master-data/by-kebun-divisi', [MasterDataController::class, 'getByKebunDivisi'])
            ->name('master-data.by-kebun-divisi');
        
        Route::get('/panen-harian/master-data', [PanenHarianController::class, 'getMasterData'])
            ->name('panen-harian.master-data');
        
        // Get unique kebun and divisi for filters
        Route::get('/kebun-list', function() {
            // Merge unique kebun from MasterData and PanenHarian to ensure full coverage
            $fromMaster = \App\Models\MasterData::select('kebun')->distinct()->pluck('kebun')->toArray();
            $fromHarian = \App\Models\PanenHarian::select('kebun')->distinct()->pluck('kebun')->toArray();
            // Normalize (trim + uppercase) to avoid duplicates due to casing/spaces
            $normalize = function($v) { return strtoupper(trim((string)$v)); };
            $merged = array_map($normalize, array_merge($fromMaster, $fromHarian));
            $merged = array_values(array_unique(array_filter($merged)));
            sort($merged);
            return response()->json($merged);
        })->name('kebun-list');
        
        Route::get('/divisi-list/{kebun?}', function($kebun = null) {
            // Merge unique divisi from MasterData and PanenHarian, optionally filtered by kebun
            $normalize = function($v) { return strtoupper(trim((string)$v)); };
            if ($kebun) {
                $k = $normalize($kebun);
                $fromMaster = \App\Models\MasterData::whereRaw('upper(trim(kebun)) = ?', [$k])
                    ->select('divisi')->distinct()->pluck('divisi')->toArray();
                $fromHarian = \App\Models\PanenHarian::whereRaw('upper(trim(kebun)) = ?', [$k])
                    ->select('divisi')->distinct()->pluck('divisi')->toArray();
            } else {
                $fromMaster = \App\Models\MasterData::select('divisi')->distinct()->pluck('divisi')->toArray();
                $fromHarian = \App\Models\PanenHarian::select('divisi')->distinct()->pluck('divisi')->toArray();
            }
            // Normalize output as well to avoid duplicates like 'DIV 1' vs 'div 1 '
            $merged = array_map($normalize, array_merge($fromMaster, $fromHarian));
            $merged = array_values(array_unique(array_filter($merged)));
            sort($merged);
            return response()->json($merged);
        })->name('divisi-list');

        // MasterData: distinct divisi (optional by kebun)
        Route::get('/master-data/divisi-list/{kebun?}', function($kebun = null) {
            $query = \App\Models\MasterData::select('divisi')->distinct()->orderBy('divisi');
            if ($kebun) {
                $query->where('kebun', $kebun);
            }
            return $query->pluck('divisi');
        })->name('master-data.divisi-list');

        // MasterData: summary statistics for cards
        Route::get('/master-data/summary', function() {
            $total = \App\Models\MasterData::count();
            $kebun = \App\Models\MasterData::select('kebun')->distinct()->count('kebun');
            $divisi = \App\Models\MasterData::select('divisi')->distinct()->count('divisi');
            $tahun = \App\Models\MasterData::select('tahun')->distinct()->count('tahun');
            return response()->json([
                'total' => $total,
                'kebun' => $kebun,
                'divisi' => $divisi,
                'tahun' => $tahun,
            ]);
        })->name('master-data.summary');
    });

    // KPI & Analytics Routes
    Route::prefix('kpi')->name('kpi.')->group(function () {
        Route::get('/', [\App\Http\Controllers\KpiController::class, 'index'])->name('index');
        Route::get('/rekonsiliasi', [\App\Http\Controllers\KpiController::class, 'rekonsiliasi'])->name('rekonsiliasi');
        Route::get('/restan', [\App\Http\Controllers\KpiController::class, 'restan'])->name('restan');
        Route::get('/budget', [\App\Http\Controllers\KpiController::class, 'budget'])->name('budget');
        Route::get('/produktifitas', [\App\Http\Controllers\KpiController::class, 'produktifitas'])->name('produktifitas');
        Route::get('/quality', [\App\Http\Controllers\KpiController::class, 'quality'])->name('quality');
        Route::get('/anomali', [\App\Http\Controllers\KpiController::class, 'anomali'])->name('anomali');
        Route::get('/summary', [\App\Http\Controllers\KpiController::class, 'summary'])->name('summary');
    });
});
