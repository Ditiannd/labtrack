<?php
// app/Http/Controllers/PeminjamanController.php
namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    // =========================================================
    // ROLE SISWA: Lihat daftar alat & ajukan peminjaman
    // =========================================================

    /**
     * Katalog alat untuk siswa
     */
    public function katalog()
    {
        $alat = Alat::where('kondisi', 'baik')
                    ->where('stok', '>', 0)
                    ->get();

        return view('siswa.katalog', compact('alat'));
    }

    /**
     * Form pengajuan peminjaman
     */
    public function create()
    {
        $alat = Alat::where('kondisi', 'baik')->where('stok', '>', 0)->get();
        return view('siswa.peminjaman.create', compact('alat'));
    }

    /**
     * Simpan pengajuan peminjaman (status: pending)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_alat'         => 'required|exists:alat,id_alat',
            'tanggal_pinjam'  => 'required|date|after_or_equal:today',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
            'jumlah'          => 'required|integer|min:1',
            'catatan_siswa'   => 'nullable|string|max:500',
        ]);

        // Ambil data siswa yang login
        $siswa = Siswa::where('id_user', Auth::id())->firstOrFail();

        // ✅ VALIDASI STOK
        $alat = Alat::findOrFail($request->id_alat);

        if ($request->jumlah > $alat->stok) {
            return back()
                ->withInput()
                ->withErrors(['jumlah' => "Stok tidak mencukupi. Stok tersedia: {$alat->stok} unit."]);
        }

        if ($alat->kondisi === 'rusak') {
            return back()
                ->withInput()
                ->withErrors(['id_alat' => 'Alat ini sedang dalam kondisi rusak dan tidak dapat dipinjam.']);
        }

        // Buat pengajuan peminjaman dengan status pending
        Peminjaman::create([
            'id_siswa'        => $siswa->id_siswa,
            'id_alat'         => $request->id_alat,
            'tanggal_pinjam'  => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'jumlah'          => $request->jumlah,
            'status'          => 'pending',
            'catatan_siswa'   => $request->catatan_siswa,
        ]);

        return redirect()->route('siswa.riwayat')
                         ->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu validasi petugas.');
    }

    /**
     * Riwayat peminjaman milik siswa yang login
     */
    public function riwayat()
    {
        $siswa = Siswa::where('id_user', Auth::id())->firstOrFail();

        $peminjaman = Peminjaman::with(['alat', 'petugas', 'pengembalian'])
                                ->where('id_siswa', $siswa->id_siswa)
                                ->latest()
                                ->paginate(10);

        return view('siswa.riwayat', compact('peminjaman'));
    }

    // =========================================================
    // ROLE PETUGAS: Validasi pengajuan & input pengembalian
    // =========================================================

    /**
     * Daftar semua pengajuan (untuk petugas)
     */
    public function daftarPengajuan()
    {
        $peminjaman = Peminjaman::with(['siswa', 'alat', 'petugas'])
                                ->orderByRaw("FIELD(status, 'pending', 'acc', 'selesai', 'ditolak')")
                                ->latest()
                                ->paginate(15);

        return view('petugas.daftar-pengajuan', compact('peminjaman'));
    }

    /**
     * ✅ ACC Peminjaman → Kurangi stok alat (dalam transaction)
     */
    public function acc(Peminjaman $peminjaman)
    {
        // Pastikan status masih pending
        if ($peminjaman->status !== 'pending') {
            return back()->withErrors(['status' => 'Pengajuan ini sudah diproses sebelumnya.']);
        }

        DB::transaction(function () use ($peminjaman) {
            $alat = Alat::lockForUpdate()->findOrFail($peminjaman->id_alat);

            // Validasi ulang stok saat approval
            if ($peminjaman->jumlah > $alat->stok) {
                throw new \Exception("Stok tidak mencukupi untuk menyetujui peminjaman ini. Stok: {$alat->stok}");
            }

            // ✅ KURANGI STOK ALAT
            $alat->decrement('stok', $peminjaman->jumlah);

            // Update status peminjaman
            $peminjaman->update([
                'status'     => 'acc',
                'id_petugas' => Auth::id(),
            ]);
        });

        return back()->with('success', 'Peminjaman berhasil disetujui dan stok alat telah dikurangi.');
    }

    /**
     * ❌ Tolak Peminjaman
     */
    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'catatan_petugas' => 'required|string|max:500',
        ]);

        if ($peminjaman->status !== 'pending') {
            return back()->withErrors(['status' => 'Pengajuan ini sudah diproses sebelumnya.']);
        }

        $peminjaman->update([
            'status'          => 'ditolak',
            'id_petugas'      => Auth::id(),
            'catatan_petugas' => $request->catatan_petugas,
        ]);

        return back()->with('success', 'Pengajuan peminjaman telah ditolak.');
    }

    // =========================================================
    // Detail peminjaman
    // =========================================================

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['siswa', 'alat', 'petugas', 'pengembalian']);
        return view('peminjaman.show', compact('peminjaman'));
    }
}