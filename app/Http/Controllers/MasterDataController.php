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
        // Base query
        $baseQuery = MasterData::query();

        // Normalisasi bulan (ID -> EN)
        if ($request->filled('bulan')) {
            $normalized = $this->monthToEnglish($request->get('bulan'));
            $request->merge(['bulan' => $normalized]);
        }

        // Filters
        if ($request->filled('tahun')) {
            $baseQuery->where('tahun', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $baseQuery->where('bulan', $request->bulan);
        }
        if ($request->filled('kebun')) {
            $baseQuery->where('kebun', 'like', '%' . $request->kebun . '%');
        }

        // DataTables server-side params
        $draw   = (int)$request->get('draw', 1);
        $start  = (int)$request->get('start', 0);          // offset
        $length = (int)$request->get('length', 25);        // page size
        if ($length <= 0) { $length = 25; }

        // Global search
        $searchValue = $request->input('search.value');
        $filteredQuery = clone $baseQuery;
        if ($searchValue) {
            $filteredQuery->where(function ($q) use ($searchValue) {
                $q->where('kebun', 'like', "%$searchValue%")
                  ->orWhere('divisi', 'like', "%$searchValue%")
                  ->orWhere('bulan', 'like', "%$searchValue%")
                  ->orWhere('tahun', 'like', "%$searchValue%");
            });
        }

        $recordsTotal = MasterData::count(); // total tanpa filter apapun
        // total setelah filter (tahun/bulan/kebun + search)
        $recordsFiltered = (clone $filteredQuery)->count();

        // Ordering (DataTables sends order[0][column], order[0][dir])
        $orderColumnIndex = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');
        $columns = [
            0 => 'kebun',
            1 => 'divisi',
            2 => 'sph_panen',
            3 => 'luas_tm',
            4 => 'budget_alokasi',
            5 => 'pkk',
            6 => 'bulan', // kita tampilkan nama_bulan_indonesia, tetap order by bulan english
            7 => 'tahun',
        ];
        if ($orderColumnIndex !== null && array_key_exists($orderColumnIndex, $columns)) {
            $col = $columns[$orderColumnIndex];
            if (!in_array(strtolower($orderDir), ['asc', 'desc'])) { $orderDir = 'asc'; }
            $filteredQuery->orderBy($col, $orderDir);
        } else {
            // Default order sama dengan definisi DataTables di view
            $filteredQuery->orderBy('tahun', 'desc')->orderBy('bulan', 'asc')->orderBy('kebun', 'asc');
        }

        // Pagination
        $data = $filteredQuery->skip($start)->take($length)->get();

        // Transform rows (tambahkan formatted & raw numeric utk sort front-end jika diperlukan)
        $rows = $data->map(function ($row) {
            return [
                'id' => $row->id,
                'kebun' => $row->kebun,
                'divisi' => $row->divisi,
                'sph_panen_raw' => $row->sph_panen,
                'sph_panen' => number_format($row->sph_panen, 0),
                'luas_tm_raw' => $row->luas_tm,
                'luas_tm' => number_format($row->luas_tm, 2) . ' Ha',
                'budget_alokasi_raw' => $row->budget_alokasi,
                'budget_alokasi' => $this->formatRupiah($row->budget_alokasi),
                'pkk_raw' => $row->pkk,
                'pkk' => number_format($row->pkk, 0),
                'bulan' => $row->bulan,
                'nama_bulan_indonesia' => $row->nama_bulan_indonesia,
                'tahun' => $row->tahun,
                'is_active' => (bool)$row->is_active,
                'created_at' => $row->created_at?->toISOString(),
                'updated_at' => $row->updated_at?->toISOString(),
                'actions' => '<div class="flex space-x-2">'
                    .'<button onclick="editRecord(' . $row->id . ')" class="text-blue-600 hover:text-blue-800"><i class="fas fa-edit"></i></button>'
                    .'<button onclick="deleteRecord(' . $row->id . ')" class="text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>'
                    .'</div>'
            ];
        });

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows,
        ]);
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
        $bulanRaw = $request->get('bulan', date('F'));
        $bulan = $this->monthToEnglish($bulanRaw);

        $masterData = MasterData::getByKebunDivisi($kebun, $divisi, $tahun, $bulan);
        
        return response()->json($masterData);
    }
    /**
     * Konversi nama bulan Indonesia -> English (untuk konsistensi penyimpanan DB)
     */
    private function monthToEnglish(string $value): string
    {
        $map = [
            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July',
            'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October',
            'November' => 'November', 'Desember' => 'December'
        ];
        return $map[$value] ?? $value;
    }

    /**
     * Format angka menjadi Rupiah singkat standar (tanpa desimal, thousand separator titik)
     */
    private function formatRupiah($number): string
    {
        return 'Rp ' . number_format((float)$number, 0, ',', '.');
    }
}
