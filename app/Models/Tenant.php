<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $table = 'tenant_tabel';

    protected $fillable = [
        'nama_booth',
        'nama_pemilik',
        'no_telp',
        'ktp',
        'booth',
        'harga_booth',
        'status_verifikasi',
        'bukti_transfer'
    ];

}