<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';
    protected $primaryKey = 'id_alat';

    protected $fillable = ['nama_alat','kategori','stok','kondisi','lokasi','deskripsi','foto'];

    public function peminjaman(){ return $this->hasMany(Peminjaman::class,'id_alat','id_alat'); }
    public function kerusakan() { return $this->hasMany(Kerusakan::class,'id_alat','id_alat'); }
}
