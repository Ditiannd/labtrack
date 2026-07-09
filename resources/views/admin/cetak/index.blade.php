@extends('layouts.app')
@section('title','Cetak Laporan')
@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:24px;">
        <div class="page-title">🖨️ Cetak Laporan</div>
        <div class="page-subtitle">Pilih laporan yang ingin dicetak atau disimpan sebagai PDF</div>
    </div>

    {{-- Stats quick view --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:24px;">
        @foreach([
            [$stats['total'],         'Total Peminjaman',     '#007AFF'],
            [$stats['belum_kembali'], 'Belum Dikembalikan',   '#FF9500'],
            [$stats['terlambat'],     'Terlambat',            '#FF3B30'],
            [$stats['alat_rusak'],    'Alat Rusak',           '#FF3B30'],
        ] as [$val,$lbl,$color])
        <div class="ios-card" style="padding:16px; text-align:center;">
            <div style="font-size:28px; font-weight:800; color:{{ $color }};">{{ $val }}</div>
            <div style="font-size:12px; color:#8E8E93; margin-top:4px;">{{ $lbl }}</div>
        </div>
        @endforeach
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

        {{-- History Peminjaman --}}
        <div class="ios-card" style="padding:22px;">
            <div style="font-size:22px; margin-bottom:8px;">📋</div>
            <div style="font-size:16px; font-weight:700; color:#1C1C1E; margin-bottom:4px;">History Peminjaman</div>
            <div style="font-size:13px; color:#8E8E93; margin-bottom:16px; line-height:1.5;">Semua riwayat peminjaman. Bisa difilter berdasarkan status dan rentang tanggal.</div>
            <form method="GET" action="{{ route('cetak.history') }}" target="_blank">
                <div style="display:grid; gap:10px; margin-bottom:14px;">
                    <div>
                        <label class="ios-label">Filter Status</label>
                        <select name="status" class="ios-input ios-select">
                            <option value="">Semua Status</option>
                            <option value="pending">⏳ Pending</option>
                            <option value="acc">✅ Disetujui</option>
                            <option value="selesai">🏁 Selesai</option>
                            <option value="ditolak">❌ Ditolak</option>
                        </select>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                        <div>
                            <label class="ios-label">Dari Tanggal</label>
                            <input type="date" name="dari" class="ios-input">
                        </div>
                        <div>
                            <label class="ios-label">Sampai</label>
                            <input type="date" name="sampai" class="ios-input">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">
                    🖨️ Cetak History
                </button>
            </form>
        </div>

        {{-- Belum Dikembalikan --}}
        <div class="ios-card" style="padding:22px;">
            <div style="font-size:22px; margin-bottom:8px;">🔄</div>
            <div style="font-size:16px; font-weight:700; color:#1C1C1E; margin-bottom:4px;">Belum Dikembalikan</div>
            <div style="font-size:13px; color:#8E8E93; margin-bottom:16px; line-height:1.5;">
                Daftar {{ $stats['belum_kembali'] }} peminjaman yang masih aktif dan belum dikembalikan.
                @if($stats['terlambat'] > 0)
                <span style="color:#FF3B30; font-weight:600;">{{ $stats['terlambat'] }} di antaranya terlambat.</span>
                @endif
            </div>
            <a href="{{ route('cetak.belum-kembali') }}" target="_blank" class="btn-primary" style="text-decoration:none; display:block; text-align:center; margin-bottom:10px;">
                🖨️ Cetak Semua
            </a>
            <a href="{{ route('cetak.terlambat') }}" target="_blank"
               style="text-decoration:none; display:block; text-align:center; padding:10px; background:rgba(255,59,48,0.08); color:#FF3B30; border-radius:12px; font-size:14px; font-weight:600;">
                ⚠️ Cetak Yang Terlambat Saja ({{ $stats['terlambat'] }})
            </a>
        </div>

        {{-- Inventaris Alat --}}
        <div class="ios-card" style="padding:22px;">
            <div style="font-size:22px; margin-bottom:8px;">🔬</div>
            <div style="font-size:16px; font-weight:700; color:#1C1C1E; margin-bottom:4px;">Inventaris Alat Lab</div>
            <div style="font-size:13px; color:#8E8E93; margin-bottom:16px; line-height:1.5;">Cetak daftar lengkap alat laboratorium dikelompokkan per kategori.</div>
            <form method="GET" action="{{ route('cetak.inventaris') }}" target="_blank">
                <div style="display:grid; gap:10px; margin-bottom:14px;">
                    <div>
                        <label class="ios-label">Filter Kategori</label>
                        <select name="kategori" class="ios-input ios-select">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ios-label">Filter Kondisi</label>
                        <select name="kondisi" class="ios-input ios-select">
                            <option value="">Semua Kondisi</option>
                            <option value="baik">✅ Baik</option>
                            <option value="rusak">❌ Rusak</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-success" style="width:100%; justify-content:center; display:flex; align-items:center; gap:6px; border-radius:12px; padding:11px; font-size:14px; font-weight:600; color:white; border:none; cursor:pointer;">
                    🖨️ Cetak Inventaris
                </button>
            </form>
        </div>

        {{-- Alat Rusak --}}
        <div class="ios-card" style="padding:22px;">
            <div style="font-size:22px; margin-bottom:8px;">⚠️</div>
            <div style="font-size:16px; font-weight:700; color:#1C1C1E; margin-bottom:4px;">Laporan Alat Rusak</div>
            <div style="font-size:13px; color:#8E8E93; margin-bottom:16px; line-height:1.5;">
                Daftar {{ $stats['alat_rusak'] }} alat rusak beserta riwayat kerusakan 50 kejadian terakhir.
            </div>
            <a href="{{ route('cetak.alat-rusak') }}" target="_blank"
               class="btn-danger" style="text-decoration:none; display:block; text-align:center; padding:11px; border-radius:12px; font-size:14px; font-weight:600;">
                🖨️ Cetak Laporan Kerusakan
            </a>
        </div>
    </div>

    {{-- Info --}}
    <div style="background:#F2F2F7; border-radius:12px; padding:14px 16px; margin-top:16px; font-size:13px; color:#8E8E93; line-height:1.6;">
        💡 Semua laporan akan terbuka di tab baru. Gunakan <strong>Ctrl+P</strong> atau tombol <strong>🖨️ Cetak</strong> di halaman laporan untuk mencetak atau simpan sebagai PDF.
    </div>
</div>
@endsection
