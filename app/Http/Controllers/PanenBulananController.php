<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PanenHarian;
use App\Models\PanenBulanan;
use App\Models\MasterData;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PanenBulananExport;

class PanenBulananController extends Controller
{
    public function index()
    {
        return view('panen-bulanan.index');
    }

    public function getData(Request $request)
    {
        // Generate monthly data from daily data
        $query = PanenHarian::selectRaw('
            tahun,
            bulan,
            kebun,
            divisi,
            SUM(luas_panen_ha) as total_luas_panen,
            SUM(jjg_panen_jjg) as total_jjg_panen,
            SUM(timbang_kebun_harian) as total_timbang_kebun,
            SUM(timbang_pks_harian) as total_timbang_pks,
            SUM(jumlah_tk_panen) as total_jumlah_tk,
            SUM(refraksi_kg) as total_refraksi_kg,
            SUM(budget_harian) as total_budget,
            SUM(tonase_panen_kg) as total_tonase,
            AVG(CASE WHEN jjg_panen_jjg > 0 THEN timbang_kebun_harian / jjg_panen_jjg ELSE 0 END) as bjr_bulanan,
            COUNT(*) as hari_panen
        ')
        ->groupBy('tahun', 'bulan', 'kebun', 'divisi');

        // Apply filters
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        if ($request->filled('kebun')) {
            $query->where('kebun', $request->kebun);
        }

        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        return DataTables::of($query)
            ->addColumn('nama_bulan_indonesia', function ($row) {
                $bulanMap = [
                    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
                ];
                return $bulanMap[$row->bulan] ?? $row->bulan;
            })
            ->addColumn('akp_bulanan', function ($row) {
                // Get SPH from master data
                $masterData = MasterData::getByKebunDivisi($row->kebun, $row->divisi, $row->tahun, $row->bulan);
                $sph = $masterData ? $masterData->sph_panen : 136;
                
                $akp = ($row->total_luas_panen * $sph) > 0 ? 
                    ($row->total_jjg_panen / ($row->total_luas_panen * $sph)) : 0;
                return number_format($akp, 4);
            })
            ->addColumn('acv_prod_bulanan', function ($row) {
                $acv = $row->total_budget > 0 ? 
                    (($row->total_timbang_pks / $row->total_budget) * 100) : 0;
                return number_format($acv, 2) . '%';
            })
            ->addColumn('selisih', function ($row) {
                $selisih = $row->total_timbang_pks - $row->total_timbang_kebun;
                $class = $selisih >= 0 ? 'text-green-600' : 'text-red-600';
                $sign = $selisih >= 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($selisih, 2) . '</span>';
            })
            ->addColumn('refraksi_persen', function ($row) {
                $refraksi = $row->total_tonase > 0 ? 
                    (($row->total_refraksi_kg / $row->total_tonase) * 100) : 0;
                return number_format($refraksi, 2) . '%';
            })
            ->editColumn('total_luas_panen', function ($row) {
                return number_format($row->total_luas_panen, 2);
            })
            ->editColumn('total_jjg_panen', function ($row) {
                return number_format($row->total_jjg_panen, 0);
            })
            ->editColumn('total_timbang_kebun', function ($row) {
                return number_format($row->total_timbang_kebun, 2);
            })
            ->editColumn('total_timbang_pks', function ($row) {
                return number_format($row->total_timbang_pks, 2);
            })
            ->editColumn('total_jumlah_tk', function ($row) {
                return number_format($row->total_jumlah_tk, 0);
            })
            ->editColumn('bjr_bulanan', function ($row) {
                return number_format($row->bjr_bulanan, 2);
            })
            ->rawColumns(['selisih'])
            ->make(true);
    }

    public function export(Request $request)
    {
        $filename = 'panen-bulanan-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new PanenBulananExport($request->all()), $filename);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|string'
        ]);

        try {
            // This would generate/update monthly summary data
            // For now, we'll just return success as the data is generated on-the-fly
            
            return redirect()->route('panen-bulanan.index')
                ->with('success', 'Data panen bulanan berhasil di-generate.');
        } catch (\Exception $e) {
            return redirect()->route('panen-bulanan.index')
                ->with('error', 'Gagal generate data: ' . $e->getMessage());
        }
    }
}
