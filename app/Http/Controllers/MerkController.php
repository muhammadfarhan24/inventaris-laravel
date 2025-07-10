<?php

namespace App\Http\Controllers;

use App\Models\MerkBarang;
use Illuminate\Http\Request;

class MerkController extends Controller
{
    public function index()
    {
        return response()->json(MerkBarang::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_merk' => 'required|string|unique:merk_barang,kode_merk',
            'nama_merk' => 'required|string'
        ]);

        $merk = MerkBarang::create($data);
        return response()->json($merk);
    }

    public function update(Request $request, $id)
    {
        $merk = MerkBarang::findOrFail($id);

        $data = $request->validate([
            'kode_merk' => 'required|string|unique:merk_barang,kode_merk,' . $id,
            'nama_merk' => 'required|string'
        ]);

        $merk->update($data);
        return response()->json($merk);
    }

    public function destroy($id)
    {
        $merk = MerkBarang::findOrFail($id);
        $merk->delete();

        return response()->json(['message' => 'Merk berhasil dihapus']);
    }
}
