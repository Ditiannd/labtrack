<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'id_pengembalian';

    protected $fillable = [
        'id_peminjaman',
        'tanggal_kembali_aktual',
        'kondisi',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }

    public function kerusakan()
    {
        return $this->hasOne(Kerusakan::class, 'id_pengembalian', 'id_pengembalian');
    }
}
