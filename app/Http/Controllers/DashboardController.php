<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PanenHarian;
use App\Models\PanenBulanan;
use App\Models\MasterData;
use App\Models\Kebun;
use App\Models\Divisi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
    // Resolve selected month/year (defaults to current)
    $selectedMonthParam = $request->get('bulan');
    $selectedYearParam = $request->get('tahun');
    $currentMonth = $selectedMonthParam ? $this->monthToNumber($selectedMonthParam) : Carbon::now()->month;
    $currentYear = $selectedYearParam ? (int)$selectedYearParam : Carbon::now()->year;
    $monthStart = Carbon::create($currentYear, $currentMonth, 1)->startOfDay();
    $monthEnd = (clone $monthStart)->endOfMonth();
    // Localized month name (ID)
    $indoMonths = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
    $summaryTitle = 'Ringkasan Bulan ' . ($indoMonths[$currentMonth] ?? date('F')) . ' ' . $currentYear;

        // Get list of kebun and divisi for filter from MasterData (current structure)
        $kebunList = MasterData::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        if ($request->filled('kebun')) {
            $divisiList = MasterData::where('kebun', $request->kebun)
                ->select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        } else {
            $divisiList = MasterData::select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        }

        // Build query with filters for today
        $query = PanenHarian::whereDate('tanggal_panen', $today);
        if ($request->filled('kebun')) {
            $query->where('kebun', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }
        // If no data for today, fallback to latest available date within current month
        $hasToday = (clone $query)->exists();
        if (!$hasToday) {
            $fallbackDateQuery = PanenHarian::whereYear('tanggal_panen', $currentYear)
                ->whereMonth('tanggal_panen', $currentMonth);
            if ($request->filled('kebun')) {
                $fallbackDateQuery->where('kebun', $request->kebun);
            }
            if ($request->filled('divisi')) {
                $fallbackDateQuery->where('divisi', $request->divisi);
            }
            $fallbackDate = $fallbackDateQuery->orderBy('tanggal_panen', 'desc')->value('tanggal_panen');
            if ($fallbackDate) {
                $query = PanenHarian::whereDate('tanggal_panen', $fallbackDate);
                if ($request->filled('kebun')) {
                    $query->where('kebun', $request->kebun);
                }
                if ($request->filled('divisi')) {
                    $query->where('divisi', $request->divisi);
                }
            }
        }

    $todayData = $query
            ->selectRaw('
                COALESCE(SUM(luas_panen_ha),0) as total_luas,
                COALESCE(SUM(jjg_panen_jjg),0) as total_jjg,
                COALESCE(SUM(timbang_kebun_harian),0) as total_timbang_kebun,
                COALESCE(SUM(timbang_pks_harian),0) as total_timbang_pks,
                COALESCE(SUM(jumlah_tk_panen),0) as total_tk,
                COALESCE(SUM(refraksi_kg),0) as total_refraksi,
        COALESCE(SUM(restant_jjg),0) as total_restan_jjg,
                COALESCE(SUM(budget_harian),0) as total_budget,
                COALESCE(SUM(tonase_panen_kg),0) as total_tonase
            ')
            ->first();

    // Build monthly query with filters (use date range to honor selected bulan/tahun)
    $monthlyQuery = PanenHarian::whereBetween('tanggal_panen', [$monthStart, $monthEnd]);
        if ($request->filled('kebun')) {
            $monthlyQuery->where('kebun', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $monthlyQuery->where('divisi', $request->divisi);
        }
    $monthlyData = $monthlyQuery
            ->selectRaw('
                COALESCE(SUM(luas_panen_ha),0) as total_luas,
                COALESCE(SUM(jjg_panen_jjg),0) as total_jjg,
                COALESCE(SUM(timbang_kebun_harian),0) as total_timbang_kebun,
                COALESCE(SUM(timbang_pks_harian),0) as total_timbang_pks,
                COALESCE(SUM(jumlah_tk_panen),0) as total_tk,
                COALESCE(SUM(refraksi_kg),0) as total_refraksi,
        COALESCE(SUM(restant_jjg),0) as total_restan_jjg,
                COALESCE(SUM(budget_harian),0) as total_budget,
                COALESCE(SUM(tonase_panen_kg),0) as total_tonase
            ')
            ->first();

        // Hitung metrik
        $todayMetrics = $this->calculateMetrics($todayData);
    $monthlyMetrics = $this->calculateMetrics($monthlyData);
    // Derived per-HK metrics for month
    $totalHk = (float)($monthlyData->total_tk ?? 0);
    $monthlyMetrics['ha_per_hk'] = $totalHk > 0 ? round(($monthlyData->total_luas ?? 0) / $totalHk, 2) : 0;
    $monthlyMetrics['ton_per_hk'] = $totalHk > 0 ? round(($monthlyData->total_timbang_pks ?? 0) / $totalHk, 2) : 0;
        // Compute JJG/PKK from MasterData for selected month/year and filters
        // MasterData stores month in English (January, February, ...)
        $monthNameEnglish = Carbon::create($currentYear, $currentMonth, 1)->format('F');
        $pkkQuery = MasterData::where('tahun', $currentYear)
            ->where('bulan', $monthNameEnglish);
        if ($request->filled('kebun')) {
            $pkkQuery->where('kebun', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $pkkQuery->where('divisi', $request->divisi);
        }
        $totalPkk = (int)$pkkQuery->sum('pkk');
        $monthlyMetrics['jjg_per_pkk'] = ($totalPkk > 0) ? round(($monthlyData->total_jjg ?? 0) / $totalPkk, 2) : 0;
        $monthlyMetrics['total_pkk'] = $totalPkk;

    // Data untuk chart (pastikan passing $request)
    $chartData = $this->getChartData($request);

        $selectedFilters = [
            'kebun' => $request->get('kebun'),
            'divisi' => $request->get('divisi'),
            'bulan' => $selectedMonthParam,
            'tahun' => $selectedYearParam,
        ];

        return view('dashboard.index', compact(
            'todayMetrics',
            'monthlyMetrics',
            'chartData',
            'kebunList',
            'divisiList',
            'summaryTitle',
            'selectedFilters'
        ));
    }

    private function calculateMetrics($data)
    {
        if (!$data) {
            return [
                'bjr' => 0,
                'akp' => 0,
                'acv_prod' => 0,
                'selisih' => 0,
                'selisih_persen' => 0,
                'refraksi_persen' => 0,
                'refraksi_kg' => 0,
                'restan_jjg' => 0,
                'restan_persen' => 0,
                'total_produksi' => 0,
                'total_tk' => 0
            ];
        }

        $bjr = $data->total_jjg > 0 ? round($data->total_timbang_kebun / $data->total_jjg, 2) : 0;
        $akp = ($data->total_luas * 136) > 0 ? round($data->total_jjg / ($data->total_luas * 136), 4) : 0;
        $acv_prod = $data->total_budget > 0 ? round(100 * $data->total_timbang_pks / $data->total_budget, 2) : 0;
        $selisih = round($data->total_timbang_pks - $data->total_timbang_kebun, 2);
        $refraksi_persen = $data->total_tonase > 0 ? round(100 * ($data->total_refraksi ?? 0) / $data->total_tonase, 2) : 0;
        $selisih_persen = $data->total_timbang_pks > 0 ? round(100 * ($data->total_timbang_pks - $data->total_timbang_kebun) / $data->total_timbang_pks, 2) : 0;
        $restan_jjg = (int)($data->total_restan_jjg ?? 0);
        $restan_persen = ($data->total_jjg ?? 0) > 0 ? round(100 * $restan_jjg / $data->total_jjg, 2) : 0;

        return [
            'bjr' => $bjr,
            'akp' => $akp,
            'acv_prod' => $acv_prod,
            'selisih' => $selisih,
            'selisih_persen' => $selisih_persen,
            'refraksi_persen' => $refraksi_persen,
            'refraksi_kg' => round(($data->total_refraksi ?? 0), 2),
            'restan_jjg' => $restan_jjg,
            'restan_persen' => $restan_persen,
            'total_produksi' => round($data->total_timbang_pks, 2),
            'total_tk' => $data->total_tk ?? 0
        ];
    }

    private function getChartData(Request $request)
    {
        // Determine month/year scope (default: current month/year, but honor provided params)
        $selectedMonth = $request->get('bulan');
        $selectedYear = $request->get('tahun');
        $monthNum = $selectedMonth ? $this->monthToNumber($selectedMonth) : Carbon::now()->month;
        $yearNum = $selectedYear ? (int)$selectedYear : Carbon::now()->year;
        $monthStart = Carbon::create($yearNum, $monthNum, 1)->startOfDay();
        $monthEnd = (clone $monthStart)->endOfMonth();

        // Base query within month for aggregation
        $baseMonth = PanenHarian::whereBetween('tanggal_panen', [$monthStart, $monthEnd])
            ->when($request->filled('kebun'), fn($q) => $q->where('kebun', $request->kebun))
            ->when($request->filled('divisi'), fn($q) => $q->where('divisi', $request->divisi));

        // Aggregate per day for PKS and Budget
        $rows = (clone $baseMonth)
            ->selectRaw('DATE(tanggal_panen) as tgl, SUM(COALESCE(timbang_pks_harian,0)) as total_pks, SUM(COALESCE(budget_harian,0)) as total_budget, SUM(COALESCE(jjg_panen_jjg,0)) as jjg_sum, SUM(COALESCE(luas_panen_ha,0)) as luas_sum')
            ->groupByRaw('DATE(tanggal_panen)')
            ->orderByRaw('DATE(tanggal_panen)')
            ->get()
            ->keyBy(function($r){ return Carbon::parse($r->tgl)->format('Y-m-d'); });

        // Build full month day series
        $cursor = $monthStart->copy();
        $dailySeries = [];
        $akpSeries = [];
        while ($cursor->lte($monthEnd)) {
            $key = $cursor->format('Y-m-d');
            $row = $rows->get($key);
            $total_pks = $row->total_pks ?? 0;
            $total_budget = $row->total_budget ?? 0;
            $jjg_sum = $row->jjg_sum ?? 0;
            $luas_sum = $row->luas_sum ?? 0;
            $akp_pct = ($luas_sum * 136) > 0 ? ($jjg_sum / ($luas_sum * 136)) * 100 : 0;
            $dailySeries[] = [
                'tanggal_panen' => $key,
                'total_pks' => (float)$total_pks,
                'total_budget' => (float)$total_budget,
            ];
            $akpSeries[] = [
                'tanggal_panen' => $key,
                'akp_pct' => (float)$akp_pct,
            ];
            $cursor->addDay();
        }

        return [
            'daily_pks_budget' => $dailySeries ?? [],
            'akp_daily' => $akpSeries ?? [],
        ];
    }

    private function normalizeMonthName(string $bulan): string
    {
        $map = [
            'JANUARI'=>'January','FEBRUARI'=>'February','MARET'=>'March','APRIL'=>'April','MEI'=>'May','JUNI'=>'June',
            'JULI'=>'July','AGUSTUS'=>'August','SEPTEMBER'=>'September','OKTOBER'=>'October','NOVEMBER'=>'November','DESEMBER'=>'December',
        ];
        $key = strtoupper(trim($bulan));
        return $map[$key] ?? Carbon::now()->format('F');
    }

    private function monthToNumber(string $bulan): int
    {
        $map = [
            'JANUARI'=>1,'FEBRUARI'=>2,'MARET'=>3,'APRIL'=>4,'MEI'=>5,'JUNI'=>6,
            'JULI'=>7,'AGUSTUS'=>8,'SEPTEMBER'=>9,'OKTOBER'=>10,'NOVEMBER'=>11,'DESEMBER'=>12,
            'JANUARY'=>1,'FEBRUARY'=>2,'MARCH'=>3,'APRIL'=>4,'MAY'=>5,'JUNE'=>6,
            'JULY'=>7,'AUGUST'=>8,'SEPTEMBER'=>9,'OCTOBER'=>10,'NOVEMBER'=>11,'DECEMBER'=>12,
        ];
        $key = strtoupper(trim($bulan));
        return $map[$key] ?? Carbon::now()->month;
    }
}