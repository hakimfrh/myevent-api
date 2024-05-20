<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    protected $primaryKey = 'id_event';

    protected $fillable = [
        'nama_event',
        'penyelenggara_event',
        'upload_ktp',
        'kategori_event',
        'tanggal_event',
        'jam_event',
        'tanggal_pendaftaran',
        'tanggal_penutupan',
        'deskripsi',
        'alamat',
        'longitude',
        'latitude',
        'upload_denah',
        'upload_pamflet',
        'no_rekening',
        'nama_rekening',
        'nama_bank',
        'email',
        'instagram',
        'whatsapp',
        'user_id'
    ];
    public function booths()
    {
        return $this->hasMany(Booth::class, 'id_event', 'id_event');
    }
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}