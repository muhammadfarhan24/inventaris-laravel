<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerkBarang extends Model
{
    protected $table = 'merk_barang';
    public $timestamps = false;

    protected $fillable = [
        'kode_merk',
        'nama_merk'
    ];
}

