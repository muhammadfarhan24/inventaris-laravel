<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;

class RuanganController extends Controller
{
    public function index()
    {
        return response()->json(Ruangan::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'jenis' => 'required|in:gudang,sekretariat,ruangan lantai 2'
        ]);

        $ruangan = Ruangan::create($data);
        return response()->json($ruangan);
    }

    public function update(Request $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $data = $request->validate([
            'nama' => 'required|string',
            'jenis' => 'required|in:gudang,sekretariat,ruangan lantai 2'
        ]);

        $ruangan->update($data);
        return response()->json($ruangan);
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $ruangan->delete();

        return response()->json(['message' => 'Ruangan berhasil dihapus']);
    }
}
