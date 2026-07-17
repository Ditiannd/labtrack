<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function katalog(Request $request)
    {
        $alat = Alat::query()
            ->when($request->q, fn($q, $s) => $q->where('nama_alat','like',"%$s%")->orWhere('lokasi','like',"%$s%"))
            ->when($request->kondisi, fn($q, $k) => $q->where('kondisi', $k))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('siswa.katalog', compact('alat'));
    }

    public function createPeminjaman(Request $request)
    {
        $alat = Alat::where('kondisi','baik')->where('stok','>',0)->get();
        $selectedAlat = $request->alat_id ? Alat::find($request->alat_id) : null;
        return view('siswa.peminjaman.create', compact('alat','selectedAlat'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'id_alat'          => 'required|exists:alat,id_alat',
            'tanggal_pinjam'   => 'required|date|after_or_equal:today',
            'tanggal_kembali'  => 'required|date|after:tanggal_pinjam',
            'jumlah'           => 'required|integer|min:1',
            'catatan_siswa'    => 'nullable|string|max:500',
        ]);

        $alat = Alat::findOrFail($request->id_alat);

        if ($alat->stok < $request->jumlah) {
            return back()->withErrors(['jumlah' => "Stok tidak mencukupi. Tersedia: {$alat->stok} unit."])->withInput();
        }

        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return back()->with('error', 'Data siswa tidak ditemukan.');
        }

        // Cek apakah siswa sudah punya peminjaman aktif untuk alat yang sama
        $aktif = Peminjaman::where('id_siswa', $siswa->id_siswa)
            ->where('id_alat', $request->id_alat)
            ->whereIn('status', ['pending','acc'])
            ->exists();

        if ($aktif) {
            return back()->with('error', 'Anda masih memiliki peminjaman aktif untuk alat ini.');
        }

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
            ->with('success', "Pengajuan peminjaman '{$alat->nama_alat}' berhasil dikirim. Tunggu validasi petugas.");
    }

    public function riwayat(Request $request)
    {
        $siswa = auth()->user()->siswa;
        $filter = $request->get('filter','semua');

        $peminjaman = Peminjaman::with(['alat','pengembalian'])
            ->where('id_siswa', $siswa->id_siswa)
            ->when($filter !== 'semua', fn($q) => $q->where('status', $filter))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('siswa.riwayat', compact('peminjaman'));
    }
}
