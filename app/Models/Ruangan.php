<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jenis' // enum: gudang, sekretariat, ruangan lantai 2
    ];
}
