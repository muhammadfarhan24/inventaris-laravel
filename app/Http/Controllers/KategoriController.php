<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class KategoriController extends Controller
{
    public function index()
    {
        return response()->json(KategoriBarang::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_kategori' => 'required|string|unique:kategori_barang,kode_kategori',
            'nama_kategori' => 'required|string'
        ]);

        $kategori = KategoriBarang::create($data);
        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'kode_kategori' => 'required|string|unique:kategori_barang,kode_kategori,' . $id,
            'nama_kategori' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kategori = KategoriBarang::findOrFail($id);
        $kategori->update($data);

        return response()->json($kategori);
    }


    public function destroy($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
