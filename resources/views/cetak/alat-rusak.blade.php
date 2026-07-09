@extends('cetak.layout')
@section('content')
<div class="laporan-header">
    <div class="laporan-header-left">
        <h1>LabTrack</h1>
        <h2>{{ $title }}</h2>
        <p>Data per {{ now()->format('d F Y') }}</p>
    </div>
    <div class="laporan-header-right">
        <strong>Tanggal Cetak</strong>
        {{ now()->format('d F Y, H:i') }} WIB
    </div>
</div>

@if($alat->count() > 0)
<div class="danger-box">
    🔧 <strong>{{ $alat->count() }} alat</strong> tercatat dalam kondisi rusak dan perlu perbaikan segera.
</div>
@endif

<div style="font-size:13px; font-weight:700; color:#1C1C1E; margin-bottom:8px;">Daftar Alat Rusak</div>
<table>
    <thead>
        <tr><th>No</th><th>Nama Alat</th><th>Kategori</th><th>Lokasi</th><th>Stok</th><th>Status Perbaikan</th></tr>
    </thead>
    <tbody>
        @forelse($alat as $i => $a)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $a->nama_alat }}</strong></td>
            <td>{{ $a->kategori ?? '-' }}</td>
            <td>{{ $a->lokasi ?? '-' }}</td>
            <td style="text-align:center; font-weight:700; color:#FF3B30;">{{ $a->stok }}</td>
            <td><span style="background:#FFEBEE; color:#B71C1C; padding:2px 8px; border-radius:8px; font-size:10px; font-weight:700;">RUSAK</span></td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center; padding:20px; color:#34C759;">Tidak ada alat rusak ✅</td></tr>
        @endforelse
    </tbody>
</table>

@if($kerusakan->count() > 0)
<div style="font-size:13px; font-weight:700; color:#1C1C1E; margin:20px 0 8px;">Riwayat Kerusakan (50 Terakhir)</div>
<table>
    <thead>
        <tr><th>No</th><th>Tanggal</th><th>Alat</th><th>Dilaporkan oleh Siswa</th><th>Deskripsi Kerusakan</th></tr>
    </thead>
    <tbody>
        @foreach($kerusakan as $i => $k)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $k->tanggal?->format('d/m/Y') }}</td>
            <td><strong>{{ $k->alat->nama_alat ?? '-' }}</strong></td>
            <td>{{ $k->pengembalian?->peminjaman?->siswa?->nama ?? '-' }}</td>
            <td>{{ $k->deskripsi }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<div class="ttd-area">
    <div class="ttd-box">
        <p>Kepala Laboratorium</p>
        <div class="ttd-line"></div>
        <p><strong>( _________________________ )</strong></p>
    </div>
</div>
@endsection
