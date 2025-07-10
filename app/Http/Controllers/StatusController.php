<?php
namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    // Endpoint untuk mendapatkan daftar status barang
    public function getStatus()
    {
        // Daftar status barang (ini bisa saja diambil dari tabel jika status lebih kompleks)
        $statusList = [
            ['status' => 'Tersedia', 'jumlah' => Barang::where('status', 'Tersedia')->count()],
            ['status' => 'Dipinjam', 'jumlah' => Barang::where('status', 'Dipinjam')->count()],
            ['status' => 'Rusak', 'jumlah' => Barang::where('status', 'Rusak')->count()],
        ];

        return response()->json($statusList);
    }

    // Endpoint untuk mendapatkan barang berdasarkan status
    public function getBarangByStatus($status)
    {
        $barangList = Barang::where('status', $status)->get();

        // Debugging
        dd($barangList);

        return response()->json($barangList);
    }

}
