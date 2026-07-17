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

@if($query->count() > 0)
<div class="danger-box">
    🚨 <strong>Terdapat {{ $query->count() }} peminjaman yang melewati batas waktu pengembalian. Segera hubungi siswa terkait.</strong>
</div>
@endif

<table>
    <thead>
        <tr>
            <th>No</th><th>Nama Siswa</th><th>NIS</th><th>Kelas</th><th>No. HP / Kontak</th>
            <th>Alat</th><th>Jml</th><th>Batas Kembali</th><th>Keterlambatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($query as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $p->siswa->nama ?? '-' }}</strong></td>
            <td style="font-family:monospace;">{{ $p->siswa->nis ?? '-' }}</td>
            <td>{{ $p->siswa->kelas ?? '-' }}</td>
            <td style="color:#8E8E93;">—</td>
            <td><strong>{{ $p->alat->nama_alat ?? '-' }}</strong></td>
            <td style="text-align:center; font-weight:700;">{{ $p->jumlah }}</td>
            <td style="color:#FF3B30; font-weight:700;">{{ $p->tanggal_kembali?->format('d/m/Y') }}</td>
            <td><span class="badge badge-ditolak">{{ now()->diffInDays($p->tanggal_kembali) }} hari</span></td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center; padding:20px; color:#34C759;">Tidak ada peminjaman terlambat ✅</td></tr>
        @endforelse
    </tbody>
</table>
<div class="ttd-area">
    <div class="ttd-box">
        <p>Petugas Laboratorium</p>
        <div class="ttd-line"></div>
        <p><strong>( _________________________ )</strong></p>
    </div>
</div>
@endsection
