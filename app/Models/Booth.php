<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booth extends Model
{
    protected $table = 'booths';

    protected $primaryKey = 'id_booth';
    
    protected $fillable = [
        'upload_gambar_booth',
        'tipe_booth',
        'harga_booth',
        'jumlah_booth',
        'deskripsi_booth',
        'id_event',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'id_event', 'id_event');
    }
}