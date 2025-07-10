<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    // Endpoint untuk mendapatkan daftar status barang
    public function getStatus()
    {
        // Daftar status barang beserta jumlah dan list barangnya
        $statusList = [
            [
                'status' => 'Tersedia',
                'jumlah' => Barang::where('status', 'Tersedia')->count(),
                'barang' => Barang::with(['kategori', 'merk', 'ruangan'])->where('status', 'Tersedia')->get(),
            ],
            [
                'status' => 'Dipinjam',
                'jumlah' => Barang::where('status', 'Dipinjam')->count(),
                'barang' => Barang::with(['kategori', 'merk', 'ruangan'])->where('status', 'Dipinjam')->get(),
            ],
            [
                'status' => 'Rusak',
                'jumlah' => Barang::where('status', 'Rusak')->count(),
                'barang' => Barang::with(['kategori', 'merk', 'ruangan'])->where('status', 'Rusak')->get(),
            ],
        ];

        return response()->json($statusList);
    }

    // Endpoint untuk mendapatkan barang berdasarkan status
    public function getBarangByStatus($status)
    {
        // Validasi status
        if (!in_array($status, ['Tersedia', 'Dipinjam', 'Rusak'])) {
            return response()->json(['error' => 'Status tidak valid'], 400);
        }

        $barangList = Barang::with(['kategori', 'merk', 'ruangan'])->where('status', $status)->get();

        return response()->json($barangList);
    }
}
