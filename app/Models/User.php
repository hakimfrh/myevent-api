<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_lengkap',
        'no_telp',
        'email',
        'username',
        'password',
        'status_verifikasi',
        'jabatan',
        'nama_perusahaan',
        'alamat_perusahaan',
        'deskripsi_perusahaan',

        'firebase_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'firebase_id',
        'remember_token',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
    ];
    public function events()
    {
        return $this->hasMany(Event::class, 'id', 'id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id');
    }

    public function totalOrders()
    {
        return $this->orders()->count();
    }

    public function thisMonthOrders()
    {
        return $this->orders()
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', date('m'))
            ->count();
    }
}