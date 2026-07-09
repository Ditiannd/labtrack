<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';
    protected $primaryKey = 'id_siswa';

    protected $fillable = ['nama','nis','kelas','jurusan','angkatan','id_user'];

    public function user()      { return $this->belongsTo(User::class, 'id_user'); }
    public function peminjaman(){ return $this->hasMany(Peminjaman::class, 'id_siswa','id_siswa'); }
}
