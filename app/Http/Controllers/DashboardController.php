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
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentMonthName = Carbon::now()->format('F');

        // Get list of kebun and divisi for filter
        $kebunList = Kebun::orderBy('nama_kebun')->get();
        $divisiList = Divisi::orderBy('nama_divisi')->get();

        // Build query with filters for today
        $query = PanenHarian::whereDate('tanggal_panen', $today);
        if ($request->filled('kebun')) {
            $query->where('kebun', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }
        $todayData = $query
            ->selectRaw('
                SUM(luas_panen_ha) as total_luas,
                SUM(jjg_panen_jjg) as total_jjg,
                SUM(timbang_kebun_harian) as total_timbang_kebun,
                SUM(timbang_pks_harian) as total_timbang_pks,
                SUM(jumlah_tk_panen) as total_tk,
                SUM(refraksi_kg) as total_refraksi,
                SUM(budget_harian) as total_budget,
                SUM(tonase_panen_kg) as total_tonase
            ')
            ->first();

        // Build monthly query with filters
        $monthlyQuery = PanenHarian::where('tahun', $currentYear)
            ->where('bulan', $currentMonthName);
        if ($request->filled('kebun')) {
            $monthlyQuery->where('kebun', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $monthlyQuery->where('divisi', $request->divisi);
        }
        $monthlyData = $monthlyQuery
            ->selectRaw('
                SUM(luas_panen_ha) as total_luas,
                SUM(jjg_panen_jjg) as total_jjg,
                SUM(timbang_kebun_harian) as total_timbang_kebun,
                SUM(timbang_pks_harian) as total_timbang_pks,
                SUM(jumlah_tk_panen) as total_tk,
                SUM(refraksi_kg) as total_refraksi,
                SUM(budget_harian) as total_budget,
                SUM(tonase_panen_kg) as total_tonase
            ')
            ->first();

        // Hitung metrik
        $todayMetrics = $this->calculateMetrics($todayData);
        $monthlyMetrics = $this->calculateMetrics($monthlyData);

        // Data untuk chart (pastikan passing $request)
        $chartData = $this->getChartData($request);

        return view('dashboard.index', compact(
            'todayMetrics',
            'monthlyMetrics',
            'chartData',
            'kebunList',
            'divisiList'
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
                'refraksi_persen' => 0,
                'total_produksi' => 0,
                'total_tk' => 0
            ];
        }

        $bjr = $data->total_jjg > 0 ? round($data->total_timbang_kebun / $data->total_jjg, 2) : 0;
        $akp = ($data->total_luas * 136) > 0 ? round($data->total_jjg / ($data->total_luas * 136), 4) : 0;
        $acv_prod = $data->total_budget > 0 ? round(100 * $data->total_timbang_pks / $data->total_budget, 2) : 0;
        $selisih = round($data->total_timbang_pks - $data->total_timbang_kebun, 2);
        $refraksi_persen = $data->total_tonase > 0 ? round(100 * $data->total_refraksi / $data->total_tonase, 2) : 0;

        return [
            'bjr' => $bjr,
            'akp' => $akp,
            'acv_prod' => $acv_prod,
            'selisih' => $selisih,
            'refraksi_persen' => $refraksi_persen,
            'total_produksi' => round($data->total_timbang_pks, 2),
            'total_tk' => $data->total_tk ?? 0
        ];
    }

    private function getChartData(Request $request)
    {
        // Build query for last 7 days
        $query7Days = PanenHarian::whereDate('tanggal_panen', '>=', Carbon::now()->subDays(7));
        if ($request->filled('kebun')) {
            $query7Days->where('kebun_id', $request->kebun);
        }
        if ($request->filled('divisi')) {
            $query7Days->where('divisi_id', $request->divisi);
        }

        // Data 7 hari terakhir
        $last7Days = $query7Days
            ->selectRaw('tanggal_panen, SUM(timbang_pks_harian) as total_produksi')
            ->groupBy('tanggal_panen')
            ->orderBy('tanggal_panen')
            ->get();

        // Data produksi per kebun bulan ini
        $currentMonthName = Carbon::now()->format('F');
        $currentYear = Carbon::now()->year;

        $productionByKebun = PanenHarian::where('tahun', $currentYear)
            ->where('bulan', $currentMonthName)
            ->selectRaw('kebun, SUM(timbang_pks_harian) as total_produksi')
            ->groupBy('kebun')
            ->get();

        return [
            'daily_production' => $last7Days,
            'production_by_kebun' => $productionByKebun
        ];
    }
}