<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'peminjam',
        'tanggal_peminjam',
        'tanggal_kembali',
        'status_pengembalian'
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
