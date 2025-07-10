<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with(['kategori', 'merk', 'ruangan'])->get();
        return response()->json($barang);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'kategori_id' => 'nullable|exists:kategori_barang,id',
            'merk_id' => 'nullable|exists:merk_barang,id',
            'status' => 'required|string',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'deskripsi' => 'required|string',
            'kode_barang' => 'required|integer',
        ]);

        $barang = Barang::create($data);
        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string',
            'kategori_id' => 'nullable|exists:kategori_barang,id',
            'merk_id' => 'nullable|exists:merk_barang,id',
            'status' => 'required|string',
            'ruangan_id' => 'nullable|exists:ruangan,id',
            'deskripsi' => 'required|string',
            'kode_barang' => 'required|integer',
        ]);

        $barang->update($data);
        return response()->json(['message' => 'Data barang berhasil diupdate', 'data' => $barang]);
    }
    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return response()->json(['message' => 'Data barang berhasil dihapus']);
    }

}
