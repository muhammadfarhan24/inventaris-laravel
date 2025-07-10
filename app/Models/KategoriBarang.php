<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriBarang extends Model
{
    protected $table = 'kategori_barang';
    public $timestamps = false;

    protected $fillable = [
        'kode_kategori',
        'nama_kategori'
    ];
}
