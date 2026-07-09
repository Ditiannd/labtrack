<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function daftarPengajuan(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        $peminjaman = Peminjaman::with(['siswa','alat'])
            ->when($filter !== 'semua', fn($q) => $q->where('status', $filter))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('petugas.daftar-pengajuan', compact('peminjaman'));
    }

    public function acc(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status sudah diproses sebelumnya.');
        }

        $alat = $peminjaman->alat;
        if (!$alat || $alat->stok < $peminjaman->jumlah) {
            return back()->with('error', 'Stok tidak mencukupi untuk disetujui.');
        }

        $alat->decrement('stok', $peminjaman->jumlah);

        $peminjaman->update([
            'status'     => 'acc',
            'id_petugas' => auth()->id(),
        ]);

        return back()->with('success', "Peminjaman oleh {$peminjaman->siswa->nama} disetujui. Stok dikurangi {$peminjaman->jumlah} unit.");
    }

    public function tolak(Request $request, Peminjaman $peminjaman)
    {
        $request->validate(['catatan_petugas' => 'required|string|max:500']);

        if ($peminjaman->status !== 'pending') {
            return back()->with('error', 'Status sudah diproses sebelumnya.');
        }

        $peminjaman->update([
            'status'          => 'ditolak',
            'id_petugas'      => auth()->id(),
            'catatan_petugas' => $request->catatan_petugas,
        ]);

        return back()->with('success', "Pengajuan berhasil ditolak.");
    }
}
