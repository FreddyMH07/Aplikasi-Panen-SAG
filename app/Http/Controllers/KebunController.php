<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kebun;

class KebunController extends Controller
{
    public function index()
    {
        $kebuns = Kebun::orderBy('nama_kebun')->get();
        return view('master.kebun.index', compact('kebuns'));
    }

    public function create()
    {
        return view('master.kebun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kebun' => 'required|string|max:255',
            'kode_kebun' => 'required|string|max:50|unique:kebuns',
            'alamat' => 'nullable|string',
            'luas_total' => 'nullable|numeric|min:0',
            'sph_panen' => 'required|integer|min:1',
        ]);

        Kebun::create($request->all());

        return redirect()->route('master.kebun.index')
            ->with('success', 'Data kebun berhasil ditambahkan.');
    }

    public function show(Kebun $kebun)
    {
        $kebun->load('divisis');
        return view('master.kebun.show', compact('kebun'));
    }

    public function edit(Kebun $kebun)
    {
        return view('master.kebun.edit', compact('kebun'));
    }

    public function update(Request $request, Kebun $kebun)
    {
        $request->validate([
            'nama_kebun' => 'required|string|max:255',
            'kode_kebun' => 'required|string|max:50|unique:kebuns,kode_kebun,' . $kebun->id,
            'alamat' => 'nullable|string',
            'luas_total' => 'nullable|numeric|min:0',
            'sph_panen' => 'required|integer|min:1',
        ]);

        $kebun->update($request->all());

        return redirect()->route('master.kebun.index')
            ->with('success', 'Data kebun berhasil diperbarui.');
    }

    public function destroy(Kebun $kebun)
    {
        try {
            $kebun->delete();
            return response()->json(['success' => true, 'message' => 'Data kebun berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data kebun.']);
        }
    }
}
