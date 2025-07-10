<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index()
    {
        return response()->json(
            Peminjaman::with('barang')->orderBy('id', 'desc')->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'peminjam' => 'required|string',
            'tanggal_peminjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_peminjam'
        ]);

        $data['status_pengembalian'] = 'Menunggu';

        $peminjaman = Peminjaman::create($data);
        return response()->json($peminjaman);
    }

    public function update(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $data = $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'peminjam' => 'required|string',
            'tanggal_peminjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_peminjam'
        ]);

        $peminjaman->update($data);
        return response()->json($peminjaman);
    }

    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        return response()->json(['message' => 'Peminjaman berhasil dihapus']);
    }

    public function setujui($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_pengembalian = 'Dipinjam';
        $peminjaman->status_peminjaman = 'Disetujui';
        $peminjaman->save();

        return response()->json(['message' => 'Peminjaman disetujui']);
    }

    public function tolak($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_peminjaman = 'Ditolak'; // Pastikan enum sudah ada 'Ditolak'
        $peminjaman->status_pengembalian = 'Ditolak';
        $peminjaman->save();

        return response()->json(['message' => 'Peminjaman ditolak.']);
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->status_pengembalian = 'Dikembalikan';
        $peminjaman->save();

        return response()->json(['message' => 'Barang dikembalikan']);
    }
}
