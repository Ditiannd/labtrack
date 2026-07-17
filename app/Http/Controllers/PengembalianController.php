<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Kerusakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function create(Peminjaman $peminjaman)
    {
        if ($peminjaman->status !== 'acc') {
            return redirect()->route('petugas.daftar-pengajuan')
                ->with('error', 'Peminjaman ini belum disetujui atau sudah dikembalikan.');
        }
        return view('petugas.pengembalian.create', compact('peminjaman'));
    }

    public function store(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'tanggal_kembali_aktual' => 'required|date',
            'kondisi'                => 'required|in:baik,rusak',
            'deskripsi_kerusakan'    => 'nullable|required_if:kondisi,rusak|string',
            'catatan'                => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $peminjaman) {
            // Simpan pengembalian
            $pengembalian = Pengembalian::create([
                'id_peminjaman'          => $peminjaman->id_peminjaman,
                'tanggal_kembali_aktual' => $request->tanggal_kembali_aktual,
                'kondisi'                => $request->kondisi,
                'catatan'                => $request->catatan,
            ]);

            // Update status peminjaman
            $peminjaman->update(['status' => 'selesai']);

            // Kembalikan stok
            $peminjaman->alat->increment('stok', $peminjaman->jumlah);

            // Jika rusak, update kondisi alat & catat kerusakan
            if ($request->kondisi === 'rusak') {
                $peminjaman->alat->update(['kondisi' => 'rusak']);

                Kerusakan::create([
                    'id_pengembalian'   => $pengembalian->id_pengembalian,
                    'id_alat'           => $peminjaman->id_alat,
                    'deskripsi'         => $request->deskripsi_kerusakan,
                    'tanggal'           => $request->tanggal_kembali_aktual,
                ]);
            }
        });

        return redirect()->route('petugas.daftar-pengajuan')
            ->with('success', "Pengembalian alat dari {$peminjaman->siswa->nama} berhasil dicatat.");
    }
}
