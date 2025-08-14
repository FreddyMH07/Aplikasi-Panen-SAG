<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterDataExport;
use App\Imports\MasterDataImport;

class MasterDataController extends Controller
{
    public function index()
    {
        return view('master.master-data.index');
    }

    public function getData(Request $request)
    {
        $query = MasterData::query();

        // Filter berdasarkan tahun
        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        // Filter berdasarkan bulan
        if ($request->filled('bulan')) {
            $query->where('bulan', $request->bulan);
        }

        // Filter berdasarkan kebun
        if ($request->filled('kebun')) {
            $query->where('kebun', 'like', '%' . $request->kebun . '%');
        }

        return DataTables::of($query)
            ->addColumn('nama_bulan_indonesia', function ($row) {
                return $row->nama_bulan_indonesia;
            })
            ->addColumn('actions', function ($row) {
                return '
                    <div class="flex space-x-2">
                        <button onclick="editRecord(' . $row->id . ')" 
                                class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteRecord(' . $row->id . ')" 
                                class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('sph_panen', function ($row) {
                return number_format($row->sph_panen, 0);
            })
            ->editColumn('luas_tm', function ($row) {
                return number_format($row->luas_tm, 2) . ' Ha';
            })
            ->editColumn('budget_alokasi', function ($row) {
                return 'Rp ' . number_format($row->budget_alokasi, 0, ',', '.');
            })
            ->editColumn('pkk', function ($row) {
                return number_format($row->pkk, 0);
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        $bulanList = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        return view('master.master-data.create', compact('bulanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'sph_panen' => 'required|numeric|min:0',
            'luas_tm' => 'required|numeric|min:0',
            'budget_alokasi' => 'required|numeric|min:0',
            'pkk' => 'required|integer|min:0',
            'bulan' => 'required|string|max:16',
            'tahun' => 'required|integer|min:2020|max:2050',
        ], [
            'kebun.required' => 'Nama kebun harus diisi',
            'divisi.required' => 'Nama divisi harus diisi',
            'sph_panen.required' => 'SPH Panen harus diisi',
            'luas_tm.required' => 'Luas TM harus diisi',
            'budget_alokasi.required' => 'Budget alokasi harus diisi',
            'pkk.required' => 'PKK harus diisi',
            'bulan.required' => 'Bulan harus dipilih',
            'tahun.required' => 'Tahun harus diisi',
        ]);

        // Check for duplicate
        $exists = MasterData::where('kebun', $request->kebun)
                           ->where('divisi', $request->divisi)
                           ->where('tahun', $request->tahun)
                           ->where('bulan', $request->bulan)
                           ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'Data untuk kebun, divisi, tahun, dan bulan tersebut sudah ada.'])
                ->withInput();
        }

        MasterData::create($request->all());

        return redirect()->route('master.master-data.index')
            ->with('success', 'Data master berhasil ditambahkan.');
    }

    public function show($id)
    {
        $masterData = MasterData::findOrFail($id);
        return response()->json($masterData);
    }

    public function edit($id)
    {
        $masterData = MasterData::findOrFail($id);
        $bulanList = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
        ];
        
        return view('master.master-data.edit', compact('masterData', 'bulanList'));
    }

    public function update(Request $request, $id)
    {
        $masterData = MasterData::findOrFail($id);
        
        $request->validate([
            'kebun' => 'required|string|max:64',
            'divisi' => 'required|string|max:64',
            'sph_panen' => 'required|numeric|min:0',
            'luas_tm' => 'required|numeric|min:0',
            'budget_alokasi' => 'required|numeric|min:0',
            'pkk' => 'required|integer|min:0',
            'bulan' => 'required|string|max:16',
            'tahun' => 'required|integer|min:2020|max:2050',
        ]);

        // Check for duplicate (excluding current record)
        $exists = MasterData::where('kebun', $request->kebun)
                           ->where('divisi', $request->divisi)
                           ->where('tahun', $request->tahun)
                           ->where('bulan', $request->bulan)
                           ->where('id', '!=', $id)
                           ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['duplicate' => 'Data untuk kebun, divisi, tahun, dan bulan tersebut sudah ada.'])
                ->withInput();
        }

        $masterData->update($request->all());

        return redirect()->route('master.master-data.index')
            ->with('success', 'Data master berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $masterData = MasterData::findOrFail($id);
            $masterData->delete();
            
            return response()->json(['success' => true, 'message' => 'Data master berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data master.']);
        }
    }

    public function export(Request $request)
    {
        $filename = 'master-data-' . date('Y-m-d') . '.xlsx';
        return Excel::download(new MasterDataExport($request->all()), $filename);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MasterDataImport, $request->file('file'));
            
            return redirect()->route('master.master-data.index')
                ->with('success', 'Data master berhasil diimport.');
        } catch (\Exception $e) {
            return redirect()->route('master.master-data.index')
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // API untuk mendapatkan data master berdasarkan kebun dan divisi
    public function getByKebunDivisi(Request $request)
    {
        $kebun = $request->get('kebun');
        $divisi = $request->get('divisi');
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan', date('F'));

        $masterData = MasterData::getByKebunDivisi($kebun, $divisi, $tahun, $bulan);
        
        return response()->json($masterData);
    }
}
