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
    try {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'deskripsi' => 'required|string',
            'tanggal_service' => 'required|date',
        ]);

        $barang = \App\Models\Barang::findOrFail($request->barang_id);

        $service = ServiceBarang::create([
            'barang_id' => $request->barang_id,
            'nama_barang' => $barang->nama,
            'deskripsi' => $request->deskripsi,
            'tanggal_service' => $request->tanggal_service,
            'status' => 'Service',
        ]);

        $barang->status = 'Service';
        $barang->save();

            return response()->json([
        'message' => 'Service berhasil ditambahkan.',
    ], 201); // <== penting agar tidak dianggap error

    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
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

    public function update(Request $request, $id)
{
    try {
        $service = ServiceBarang::findOrFail($id);

        if ($request->has('status')) {
            $service->status = $request->status;
            $service->save();

            // Juga update status barang terkait
            $barang = $service->barang;
            if ($barang) {
                $barang->status = $request->status;
                $barang->save();
            }

            return response()->json(['message' => 'Status service diperbarui.']);
        }

        return response()->json(['message' => 'Tidak ada data untuk diperbarui.'], 400);
    } catch (\Throwable $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
