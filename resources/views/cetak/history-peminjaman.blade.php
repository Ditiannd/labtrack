@extends('cetak.layout')
@section('content')

<div class="laporan-header">
    <div class="laporan-header-left">
        <h1>LabTrack</h1>
        <h2>{{ $title }}</h2>
        <p>
            @if(!empty($filter['status'])) Status: {{ ucfirst($filter['status']) }} @endif
            @if(!empty($filter['dari'])) | Dari: {{ $filter['dari'] }} @endif
            @if(!empty($filter['sampai'])) | Sampai: {{ $filter['sampai'] }} @endif
        </p>
    </div>
    <div class="laporan-header-right">
        <strong>Tanggal Cetak</strong>
        {{ now()->format('d F Y, H:i') }} WIB
    </div>
</div>

<div class="stats-row">
    @php
        $totalData = $query->count();
        $cPending  = $query->where('status','pending')->count();
        $cAcc      = $query->where('status','acc')->count();
        $cSelesai  = $query->where('status','selesai')->count();
        $cDitolak  = $query->where('status','ditolak')->count();
    @endphp
    <div class="stat-box"><div class="num">{{ $totalData }}</div><div class="lbl">Total</div></div>
    <div class="stat-box"><div class="num" style="color:#FF9500;">{{ $cPending }}</div><div class="lbl">Pending</div></div>
    <div class="stat-box"><div class="num" style="color:#34C759;">{{ $cAcc }}</div><div class="lbl">Dipinjam</div></div>
    <div class="stat-box"><div class="num" style="color:#5856D6;">{{ $cSelesai }}</div><div class="lbl">Selesai</div></div>
    <div class="stat-box"><div class="num" style="color:#FF3B30;">{{ $cDitolak }}</div><div class="lbl">Ditolak</div></div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Siswa</th>
            <th>Kelas / Jurusan</th>
            <th>Alat</th>
            <th>Kategori</th>
            <th>Jml</th>
            <th>Tgl Pinjam</th>
            <th>Rencana Kembali</th>
            <th>Tgl Kembali Aktual</th>
            <th>Status</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($query as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $p->siswa->nama ?? '-' }}</strong></td>
            <td>{{ $p->siswa->kelas ?? '-' }}<br><span style="color:#8E8E93;">{{ $p->siswa->jurusan ?? '' }}</span></td>
            <td><strong>{{ $p->alat->nama_alat ?? '-' }}</strong><br><span style="color:#8E8E93;">{{ $p->alat->lokasi ?? '' }}</span></td>
            <td>{{ $p->alat->kategori ?? '-' }}</td>
            <td style="text-align:center; font-weight:700;">{{ $p->jumlah }}</td>
            <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
            <td style="{{ $p->status==='acc' && $p->tanggal_kembali < now() ? 'color:#FF3B30;font-weight:700;' : '' }}">
                {{ $p->tanggal_kembali?->format('d/m/Y') }}
            </td>
            <td>{{ $p->pengembalian?->tanggal_kembali_aktual?->format('d/m/Y') ?? '-' }}</td>
            <td><span class="badge badge-{{ $p->status }}">{{ strtoupper($p->status) }}</span></td>
            <td style="font-size:10px; color:#8E8E93;">{{ Str::limit($p->catatan_siswa ?? $p->catatan_petugas ?? '-', 40) }}</td>
        </tr>
        @empty
        <tr><td colspan="11" style="text-align:center; padding:20px; color:#8E8E93;">Tidak ada data</td></tr>
        @endforelse
    </tbody>
</table>

<div class="ttd-area">
    <div class="ttd-box">
        <p>Kepala Laboratorium</p>
        <div class="ttd-line"></div>
        <p><strong>( _________________________ )</strong></p>
    </div>
</div>
@endsection
