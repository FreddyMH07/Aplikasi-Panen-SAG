<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Divisi;
use App\Models\Kebun;

class DivisiController extends Controller
{
    public function index()
    {
        $divisis = Divisi::with('kebun')->orderBy('nama_divisi')->get();
        return view('master.divisi.index', compact('divisis'));
    }

    public function create()
    {
        $kebuns = Kebun::where('is_active', true)->orderBy('nama_kebun')->get();
        return view('master.divisi.create', compact('kebuns'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'kode_divisi' => 'required|string|max:50|unique:divisis',
            'kebun_id' => 'required|exists:kebuns,id',
            'luas_divisi' => 'nullable|numeric|min:0',
        ]);

        Divisi::create($request->all());

        return redirect()->route('master.divisi.index')
            ->with('success', 'Data divisi berhasil ditambahkan.');
    }

    public function show(Divisi $divisi)
    {
        $divisi->load('kebun');
        return view('master.divisi.show', compact('divisi'));
    }

    public function edit(Divisi $divisi)
    {
        $kebuns = Kebun::where('is_active', true)->orderBy('nama_kebun')->get();
        return view('master.divisi.edit', compact('divisi', 'kebuns'));
    }

    public function update(Request $request, Divisi $divisi)
    {
        $request->validate([
            'nama_divisi' => 'required|string|max:255',
            'kode_divisi' => 'required|string|max:50|unique:divisis,kode_divisi,' . $divisi->id,
            'kebun_id' => 'required|exists:kebuns,id',
            'luas_divisi' => 'nullable|numeric|min:0',
        ]);

        $divisi->update($request->all());

        return redirect()->route('master.divisi.index')
            ->with('success', 'Data divisi berhasil diperbarui.');
    }

    public function destroy(Divisi $divisi)
    {
        try {
            $divisi->delete();
            return response()->json(['success' => true, 'message' => 'Data divisi berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data divisi.']);
        }
    }
}
