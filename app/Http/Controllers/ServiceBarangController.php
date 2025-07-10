<?php

namespace App\Http\Controllers;

use App\Models\ServiceBarang;
use App\Models\Barang;
use Illuminate\Http\Request;

class ServiceBarangController extends Controller
{
    public function index()
    {
        $data = ServiceBarang::with('barang')->get();
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'deskripsi' => 'required|string',
            'tanggal_service' => 'required|date',
        ]);
        $barang = Barang::findOrFail($request->barang_id);
        ServiceBarang::create([
            'barang_id' => $request->barang_id,
            'nama_barang' => $barang->nama,
            'deskripsi' => $request->deskripsi,
            'tanggal_service' => $request->tanggal_service,
            'status' => 'Service',
        ]);

        $barang = Barang::find($request->barang_id);
        $barang->status = 'Service';
        $barang->save();

        return response()->json(['message' => 'Barang berhasil masuk service dan status diperbarui.']);
    }

    public function destroy($id)
    {
        $service = ServiceBarang::findOrFail($id);

        // Jika perlu, update status barang kembali ke 'Tersedia'
        $barang = $service->barang;
        if ($barang) {
            $barang->status = 'Selesai';
            $barang->save();
        }

        $service->delete();

        return response()->json(['message' => 'Data service berhasil dihapus']);
    }
}
