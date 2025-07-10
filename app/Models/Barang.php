<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $table = 'barang';
    public $timestamps = false;


    protected $fillable = [
        'nama', 'kategori_id', 'merk_id', 'status', 'ruangan_id', 'deskripsi', 'kode_barang'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriBarang::class, 'kategori_id');
    }

    public function merk()
    {
        return $this->belongsTo(MerkBarang::class, 'merk_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_id');
    }
}
