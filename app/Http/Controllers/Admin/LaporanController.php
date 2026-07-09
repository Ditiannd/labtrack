<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index()
    {
        // ── Stats Utama ──────────────────────────────────────
        $stats = [
            'total_peminjaman' => Peminjaman::count(),
            'pending'          => Peminjaman::where('status','pending')->count(),
            'acc'              => Peminjaman::where('status','acc')->count(),
            'selesai'          => Peminjaman::where('status','selesai')->count(),
            'ditolak'          => Peminjaman::where('status','ditolak')->count(),
            'total_alat'       => Alat::count(),
            'total_stok'       => Alat::sum('stok'),
            'alat_rusak'       => Alat::where('kondisi','rusak')->count(),
            'total_siswa'      => Siswa::count(),
            'total_petugas'    => User::where('role','petugas')->count(),
        ];

        // ── Peminjaman terbaru ───────────────────────────────
        $peminjaman_terbaru = Peminjaman::with(['siswa','alat'])
            ->latest()->take(8)->get();

        // ── Alat paling sering dipinjam ──────────────────────
        $alat_populer = Peminjaman::selectRaw('id_alat, COUNT(*) as total')
            ->with('alat')->groupBy('id_alat')
            ->orderByDesc('total')->take(5)->get();

        // ── Peminjaman terlambat ─────────────────────────────
        $terlambat = Peminjaman::with(['siswa','alat'])
            ->where('status','acc')
            ->where('tanggal_kembali','<', now()->toDateString())
            ->get();

        // ── Trend peminjaman 7 hari terakhir ─────────────────
        $trend = Peminjaman::selectRaw('DATE(created_at) as tgl, COUNT(*) as total')
            ->where('created_at','>=', now()->subDays(6)->startOfDay())
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get()
            ->keyBy('tgl');

        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->toDateString();
            $trendData[] = [
                'label' => now()->subDays($i)->format('d M'),
                'total' => $trend->get($d)?->total ?? 0,
            ];
        }

        // ── Distribusi per jurusan ───────────────────────────
        $per_jurusan = Siswa::selectRaw('jurusan, COUNT(*) as total')
            ->whereNotNull('jurusan')
            ->groupBy('jurusan')
            ->orderByDesc('total')
            ->get();

        // ── Stok per kategori ─────────────────────────────────
        $per_kategori = Alat::selectRaw('kategori, COUNT(*) as jenis, SUM(stok) as total_stok')
            ->whereNotNull('kategori')->where('kategori','!=','')
            ->groupBy('kategori')
            ->orderByDesc('total_stok')
            ->get();

        return view('admin.laporan', compact(
            'stats','peminjaman_terbaru','alat_populer',
            'terlambat','trendData','per_jurusan','per_kategori'
        ));
    }
}
