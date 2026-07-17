<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\Kerusakan;
use Illuminate\Http\Request;

class CetakController extends Controller
{
    /** Cetak semua history peminjaman */
    public function historyPeminjaman(Request $request)
    {
        $query = Peminjaman::with(['siswa','alat','pengembalian'])
            ->when($request->status, fn($q,$s) => $q->where('status',$s))
            ->when($request->dari,   fn($q,$d) => $q->whereDate('tanggal_pinjam','>=',$d))
            ->when($request->sampai, fn($q,$d) => $q->whereDate('tanggal_pinjam','<=',$d))
            ->orderBy('tanggal_pinjam','desc')
            ->get();

        $title  = 'Laporan History Peminjaman';
        $filter = $request->only(['status','dari','sampai']);
        return view('cetak.history-peminjaman', compact('query','title','filter'));
    }

    /** Cetak peminjaman belum dikembalikan */
    public function belumKembali()
    {
        $query = Peminjaman::with(['siswa','alat'])
            ->where('status','acc')
            ->orderBy('tanggal_kembali','asc')
            ->get();

        $title = 'Laporan Peminjaman Belum Dikembalikan';
        return view('cetak.belum-kembali', compact('query','title'));
    }

    /** Cetak peminjaman terlambat */
    public function terlambat()
    {
        $query = Peminjaman::with(['siswa','alat'])
            ->where('status','acc')
            ->where('tanggal_kembali','<', now()->toDateString())
            ->orderBy('tanggal_kembali','asc')
            ->get();

        $title = 'Laporan Peminjaman Terlambat';
        return view('cetak.terlambat', compact('query','title'));
    }

    /** Cetak semua alat tersedia */
    public function inventarisAlat(Request $request)
    {
        $query = Alat::query()
            ->when($request->kondisi,  fn($q,$k) => $q->where('kondisi',$k))
            ->when($request->kategori, fn($q,$k) => $q->where('kategori',$k))
            ->orderBy('kategori')->orderBy('nama_alat')
            ->get();

        $title  = 'Laporan Inventaris Alat Laboratorium';
        $filter = $request->only(['kondisi','kategori']);
        return view('cetak.inventaris-alat', compact('query','title','filter'));
    }

    /** Cetak daftar alat rusak */
    public function alatRusak()
    {
        $alat = Alat::where('kondisi','rusak')
            ->orderBy('kategori')->orderBy('nama_alat')
            ->get();

        $kerusakan = Kerusakan::with(['alat','pengembalian.peminjaman.siswa'])
            ->orderBy('tanggal','desc')
            ->take(50)->get();

        $title = 'Laporan Alat Rusak & Riwayat Kerusakan';
        return view('cetak.alat-rusak', compact('alat','kerusakan','title'));
    }

    /** Halaman pilih cetak (form) */
    public function index()
    {
        $kategoriList = Alat::select('kategori')
            ->whereNotNull('kategori')->where('kategori','!=','')
            ->distinct()->orderBy('kategori')->pluck('kategori');

        $stats = [
            'total'         => Peminjaman::count(),
            'belum_kembali' => Peminjaman::where('status','acc')->count(),
            'terlambat'     => Peminjaman::where('status','acc')->where('tanggal_kembali','<',now()->toDateString())->count(),
            'alat_rusak'    => Alat::where('kondisi','rusak')->count(),
        ];

        return view('admin.cetak.index', compact('kategoriList','stats'));
    }
}
