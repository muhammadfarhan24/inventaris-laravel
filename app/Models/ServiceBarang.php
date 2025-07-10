<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceBarang extends Model
{
    protected $table = 'service_barang';
    public $timestamps = false;

    protected $fillable = [
        'barang_id',
        'nama_barang',
        'deskripsi',
        'tanggal_service',
        'status',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
