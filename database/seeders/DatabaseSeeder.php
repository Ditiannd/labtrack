<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Alat;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ─────────────────────────────────────────────
        User::firstOrCreate(['email'=>'admin@lab.com'],   ['name'=>'Kepala Lab',  'password'=>Hash::make('password'),'role'=>'admin']);
        User::firstOrCreate(['email'=>'petugas@lab.com'], ['name'=>'Petugas Lab', 'password'=>Hash::make('password'),'role'=>'petugas']);

        // ── Siswa ─────────────────────────────────────────────
        $siswaData = [
            ['Budi Santoso',  '2024001','X RPL 1', 'Rekayasa Perangkat Lunak',     '2024','siswa@lab.com'],
            ['Ani Rahayu',    '2024002','X RPL 1', 'Rekayasa Perangkat Lunak',     '2024','ani@lab.com'],
            ['Candra Wijaya', '2024003','X RPL 2', 'Rekayasa Perangkat Lunak',     '2024','candra@lab.com'],
            ['Dewi Rahayu',   '2024004','X TKJ 1', 'Teknik Komputer & Jaringan',   '2024','dewi@lab.com'],
            ['Eko Prasetyo',  '2024005','X TKJ 1', 'Teknik Komputer & Jaringan',   '2024','eko@lab.com'],
            ['Fitri Amalia',  '2024006','X MM 1',  'Multimedia',                   '2024','fitri@lab.com'],
        ];
        foreach ($siswaData as [$nama,$nis,$kelas,$jurusan,$angkatan,$email]) {
            $user = User::firstOrCreate(['email'=>$email], ['name'=>$nama,'password'=>Hash::make('password'),'role'=>'siswa']);
            Siswa::firstOrCreate(['nis'=>$nis], ['nama'=>$nama,'kelas'=>$kelas,'jurusan'=>$jurusan,'angkatan'=>$angkatan,'id_user'=>$user->id]);
        }

        // ── Alat ─────────────────────────────────────────────
        $alatData = [
            ['Mikroskop Binokuler', 'Optik',          8,  'baik',  'Lab Biologi', 'Untuk pengamatan sel dan jaringan'],
            ['Teleskop Astronomi',  'Optik',          3,  'baik',  'Lab Fisika',  'Pengamatan benda langit'],
            ['Buret 50 mL',         'Kimia Analitik', 10, 'baik',  'Lab Kimia',   'Alat titrasi 50 mL'],
            ['Erlenmeyer 250 mL',   'Kimia Analitik', 20, 'baik',  'Lab Kimia',   'Labu ukur percobaan kimia'],
            ['Jangka Sorong',       'Pengukuran',     5,  'baik',  'Lab Fisika',  'Alat ukur presisi tinggi'],
            ['Gelas Ukur 100 mL',   'Kimia Analitik', 15, 'baik',  'Lab Kimia',   'Gelas ukur volume cairan'],
            ['Neraca Analitik',     'Pengukuran',     2,  'baik',  'Lab Kimia',   'Timbangan presisi laboratorium'],
            ['Termometer Digital',  'Sensor',         6,  'baik',  'Lab Biologi', 'Pengukur suhu digital'],
            ['Multimeter Analog',   'Elektronika',    4,  'rusak', 'Lab Fisika',  'Alat ukur tegangan, arus, hambatan'],
            ['Power Supply DC',     'Elektronika',    3,  'baik',  'Lab Fisika',  'Sumber tegangan DC'],
        ];
        foreach ($alatData as [$nama,$kat,$stok,$kondisi,$lokasi,$desk]) {
            Alat::firstOrCreate(['nama_alat'=>$nama], ['kategori'=>$kat,'stok'=>$stok,'kondisi'=>$kondisi,'lokasi'=>$lokasi,'deskripsi'=>$desk]);
        }
    }
}
