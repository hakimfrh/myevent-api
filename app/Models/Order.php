<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   // Table name (if not following Laravel's naming conventions)
   protected $table = 'orders';

   // Primary key
   protected $primaryKey = 'id_order';

   // Specify that primary key is non-incrementing or not integer
   public $incrementing = false;
   protected $keyType = 'string';

   // Fillable fields for mass assignment
   protected $fillable = [
       'status_pembayaran',
       'harga_bayar',
       'nomor_booth',
       'img_bukti_transfer',
       'tgl_order',
       'tgl_verifikasi',
       'tgl_bayar',
       'id',
       'id_booth'
   ];
/**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
   // Relationships
   public function user()
   {
       return $this->belongsTo(User::class, 'id');
   }

   public function booth()
   {
       return $this->belongsTo(Booth::class, 'id_booth');
   }
}
