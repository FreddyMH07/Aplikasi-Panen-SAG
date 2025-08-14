<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PanenHarian;
use App\Models\MasterData;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PanenHarianExport;
use App\Imports\PanenHarianImport;
use Yajra\DataTables\Facades\DataTables;

class PanenHarianController extends Controller
{
    public function index()
    {
        // Get unique kebun and divisi for filters
        $kebuns = PanenHarian::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        
        return view('panen-harian.index', compact('kebuns'));
    }

    public function getData(Request $request)
    {
        $query = PanenHarian::query();

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_panen', [$request->start_date, $request->end_date]);
        }

        // Filter by kebun
        if ($request->filled('kebun')) {
            $query->where('kebun', $request->kebun);
        }

        // Filter by divisi
        if ($request->filled('divisi')) {
            $query->where('divisi', $request->divisi);
        }

        // Filter by tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter by bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        return DataTables::of($query)
            ->addColumn('bjr_calculated', function ($row) {
                return number_format($row->bjr, 2);
            })
            ->addColumn('akp_calculated', function ($row) {
                return number_format($row->akp_calculated * 100, 2) . '%';
            })
            ->addColumn('acv_prod', function ($row) {
                return number_format($row->acv_prod, 2) . '%';
            })
            ->addColumn('selisih', function ($row) {
                $selisih = $row->selisih;
                $class = $selisih >= 0 ? 'text-green-600' : 'text-red-600';
                $sign = $selisih >= 0 ? '+' : '';
                return '<span class="' . $class . '">' . $sign . number_format($selisih, 2) . '</span>';
            })
            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex space-x-2">
                        <a href="' . route('panen-harian.edit', $row->id) . '" 
                           class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="deleteRecord(' . $row->id . ')" 
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('tanggal_panen', function ($row) {
                return $row->tanggal_panen->format('d/m/Y');
            })
            ->editColumn('akp_panen', function ($row) {
                return $row->akp_panen ?: '-';
            })
            ->editColumn('ketrek', function ($row) {
                return $row->ketrek ?: '-';
            })
            ->editColumn('luas_panen_ha', function ($row) {
                return number_format($row->luas_panen_ha, 2);
            })
            ->editColumn('jjg_panen_jjg', function ($row) {
                return number_format($row->jjg_panen_jjg, 0);
            })
            ->editColumn('jjg_kirim_jjg', function ($row) {
                return number_format($row->jjg_kirim_jjg, 0);
            })
            ->editColumn('total_jjg_kirim_jjg', function ($row) {
                return number_format($row->total_jjg_kirim_jjg, 0);
            })
            ->editColumn('tonase_panen_kg', function ($row) {
                return number_format($row->tonase_panen_kg, 2);
            })
            ->editColumn('refraksi_kg', function ($row) {
                return number_format($row->refraksi_kg, 2);
            })
            ->editColumn('refraksi_persen', function ($row) {
                return number_format($row->refraksi_persen, 2) . '%';
            })
            ->editColumn('restant_jjg', function ($row) {
                return number_format($row->restant_jjg, 0);
            })
            ->editColumn('bjr_hari_ini', function ($row) {
                return number_format($row->bjr_hari_ini, 2);
            })
            ->editColumn('output_kg_hk', function ($row) {
                return number_format($row->output_kg_hk, 2);
            })
            ->editColumn('output_ha_hk', function ($row) {
                return number_format($row->output_ha_hk, 2);
            })
            ->editColumn('budget_harian', function ($row) {
                return number_format($row->budget_harian, 0);
            })
            ->editColumn('timbang_kebun_harian', function ($row) {
                return number_format($row->timbang_kebun_harian, 2);
            })
            ->editColumn('timbang_pks_harian', function ($row) {
                return number_format($row->timbang_pks_harian, 2);
            })
            ->editColumn('rotasi_panen', function ($row) {
                return number_format($row->rotasi_panen, 1);
            })
            ->editColumn('jumlah_tk_panen', function ($row) {
                return number_format($row->jumlah_tk_panen, 0);
            })
            ->rawColumns(['selisih', 'actions'])
            ->make(true);
    }

    public function create()
    {
        $kebuns = PanenHarian::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        $bulanList = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        return view('panen-harian.create', compact('kebuns', 'bulanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_panen' => 'required|date',
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'akp_panen' => 'nullable|string|max:8',
            'jumlah_tk_panen' => 'nullable|integer|min:0',
            'luas_panen_ha' => 'nullable|numeric|min:0',
            'jjg_panen_jjg' => 'nullable|integer|min:0',
            'jjg_kirim_jjg' => 'nullable|integer|min:0',
            'ketrek' => 'nullable|numeric|min:0',
            'total_jjg_kirim_jjg' => 'nullable|integer|min:0',
            'tonase_panen_kg' => 'nullable|numeric|min:0',
            'refraksi_kg' => 'nullable|numeric|min:0',
            'restant_jjg' => 'nullable|integer|min:0',
            'budget_harian' => 'nullable|numeric|min:0',
            'timbang_kebun_harian' => 'nullable|numeric|min:0',
            'timbang_pks_harian' => 'nullable|numeric|min:0',
            'rotasi_panen' => 'nullable|numeric|min:0',
        ]);

        // Calculate derived fields with null safety
        $data = $request->all();
        $tonase = $data['tonase_panen_kg'] ?? 0;
        $refraksi = $data['refraksi_kg'] ?? 0;
        $jjg = $data['jjg_panen_jjg'] ?? 0;
        $timbangKebun = $data['timbang_kebun_harian'] ?? 0;
        $tk = $data['jumlah_tk_panen'] ?? 0;
        $luas = $data['luas_panen_ha'] ?? 0;
        
        $data['refraksi_persen'] = $tonase > 0 ? ($refraksi / $tonase * 100) : 0;
        $data['bjr_hari_ini'] = $jjg > 0 ? ($timbangKebun / $jjg) : 0;
        $data['output_kg_hk'] = $tk > 0 ? ($tonase / $tk) : 0;
        $data['output_ha_hk'] = $tk > 0 ? ($luas / $tk) : 0;

        PanenHarian::create($data);

        return redirect()->route('panen-harian.index')
            ->with('success', 'Data panen harian berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $panenHarian = PanenHarian::findOrFail($id);
        $kebuns = PanenHarian::select('kebun')->distinct()->orderBy('kebun')->pluck('kebun');
        $divisis = PanenHarian::where('kebun', $panenHarian->kebun)
                              ->select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        $bulanList = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        return view('panen-harian.edit', compact('panenHarian', 'kebuns', 'divisis', 'bulanList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_panen' => 'required|date',
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'jumlah_tk_panen' => 'required|integer|min:0',
            'luas_panen_ha' => 'required|numeric|min:0',
            'jjg_panen_jjg' => 'required|integer|min:0',
            'jjg_kirim_jjg' => 'nullable|integer|min:0',
            'total_jjg_kirim_jjg' => 'nullable|integer|min:0',
            'tonase_panen_kg' => 'required|numeric|min:0',
            'refraksi_kg' => 'nullable|numeric|min:0',
            'restant_jjg' => 'nullable|integer|min:0',
            'budget_harian' => 'nullable|numeric|min:0',
            'timbang_kebun_harian' => 'required|numeric|min:0',
            'timbang_pks_harian' => 'required|numeric|min:0',
            'rotasi_panen' => 'nullable|numeric|min:0',
        ]);

        $panenHarian = PanenHarian::findOrFail($id);
        
        // Calculate derived fields
        $data = $request->all();
        $data['refraksi_persen'] = $data['tonase_panen_kg'] > 0 ? 
            (($data['refraksi_kg'] ?? 0) / $data['tonase_panen_kg'] * 100) : 0;
        $data['bjr_hari_ini'] = $data['jjg_panen_jjg'] > 0 ? 
            ($data['timbang_kebun_harian'] / $data['jjg_panen_jjg']) : 0;
        $data['output_kg_hk'] = $data['jumlah_tk_panen'] > 0 ? 
            ($data['tonase_panen_kg'] / $data['jumlah_tk_panen']) : 0;
        $data['output_ha_hk'] = $data['jumlah_tk_panen'] > 0 ? 
            ($data['luas_panen_ha'] / $data['jumlah_tk_panen']) : 0;

        $panenHarian->update($data);

        return redirect()->route('panen-harian.index')
            ->with('success', 'Data panen harian berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $panenHarian = PanenHarian::findOrFail($id);
            $panenHarian->delete();
            
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data.']);
        }
    }

    public function export(Request $request)
    {
        $filename = 'panen-harian-' . date('Y-m-d') . '.xlsx';
        
        return Excel::download(new PanenHarianExport($request->all()), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new PanenHarianImport, $request->file('file'));
            
            return redirect()->route('panen-harian.index')
                ->with('success', 'Data berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('panen-harian.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // API untuk mendapatkan divisi berdasarkan kebun
    public function getDivisiByKebun(Request $request)
    {
        $kebun = $request->get('kebun');
        $divisis = PanenHarian::where('kebun', $kebun)
                              ->select('divisi')->distinct()->orderBy('divisi')->pluck('divisi');
        
        return response()->json($divisis);
    }

    // API untuk mendapatkan master data
    public function getMasterData(Request $request)
    {
        $kebun = $request->get('kebun');
        $divisi = $request->get('divisi');
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('F'));

        $masterData = MasterData::getByKebunDivisi($kebun, $divisi, $tahun, $bulan);
        
        return response()->json($masterData);
    }
}
