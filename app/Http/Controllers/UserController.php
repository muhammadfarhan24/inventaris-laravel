<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Tampilkan semua user (boleh dilihat semua role)
    public function index()
    {
        return User::all();
    }

    // Tambah user (khusus admin)
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:user,username',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,ketua_masjid,ketua_yayasan',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        return response()->json($user, 201);
    }

    // Update user (khusus admin)
    public function update(Request $request, $id)
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:user,username,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:admin,ketua_masjid,ketua_yayasan',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    // Hapus user (khusus admin)
    public function destroy($id)
    {
        $this->authorizeAdmin();

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }

    // Fungsi bantu cek admin
    private function authorizeAdmin()
    {
        $user = Auth::user(); // Mendapatkan user dari sesi

        if (!$user || $user->role !== 'admin') {
            abort(403, 'Akses ditolak');
        }
    }
}
