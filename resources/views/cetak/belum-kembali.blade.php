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

@if($query->where('tanggal_kembali','<',now()->toDateString())->count() > 0)
<div class="danger-box">
    ⚠️ <strong>{{ $query->where('tanggal_kembali','<',now()->toDateString())->count() }} peminjaman sudah melewati batas waktu pengembalian!</strong>
</div>
@endif

<div class="stats-row">
    <div class="stat-box"><div class="num">{{ $query->count() }}</div><div class="lbl">Belum Kembali</div></div>
    <div class="stat-box"><div class="num" style="color:#FF3B30;">{{ $query->where('tanggal_kembali','<',now()->toDateString())->count() }}</div><div class="lbl">Terlambat</div></div>
    <div class="stat-box"><div class="num" style="color:#FF9500;">{{ $query->where('tanggal_kembali','>=',now()->toDateString())->count() }}</div><div class="lbl">Tepat Waktu</div></div>
    <div class="stat-box"><div class="num">{{ $query->sum('jumlah') }}</div><div class="lbl">Total Unit</div></div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th><th>Nama Siswa</th><th>Kelas / Jurusan</th><th>Alat Dipinjam</th>
            <th>Jml</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Keterlambatan</th><th>Catatan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($query as $i => $p)
        @php $terlambat = $p->tanggal_kembali < now(); @endphp
        <tr style="{{ $terlambat ? 'background:#FFF3F3;' : '' }}">
            <td>{{ $i+1 }}</td>
            <td><strong>{{ $p->siswa->nama ?? '-' }}</strong><br><span style="color:#8E8E93; font-size:10px;">{{ $p->siswa->nis ?? '' }}</span></td>
            <td>{{ $p->siswa->kelas ?? '-' }}<br><span style="color:#8E8E93; font-size:10px;">{{ $p->siswa->jurusan ?? '' }}</span></td>
            <td><strong>{{ $p->alat->nama_alat ?? '-' }}</strong><br><span style="color:#8E8E93; font-size:10px;">{{ $p->alat->lokasi ?? '' }}</span></td>
            <td style="text-align:center; font-weight:700;">{{ $p->jumlah }}</td>
            <td>{{ $p->tanggal_pinjam?->format('d/m/Y') }}</td>
            <td style="{{ $terlambat ? 'color:#FF3B30; font-weight:700;' : 'color:#34C759; font-weight:700;' }}">
                {{ $p->tanggal_kembali?->format('d/m/Y') }}
            </td>
            <td>
                @if($terlambat)
                    <span class="badge badge-ditolak">{{ now()->diffInDays($p->tanggal_kembali) }} hari</span>
                @else
                    <span style="color:#34C759; font-size:10px;">Sisa {{ now()->diffInDays($p->tanggal_kembali) }}h</span>
                @endif
            </td>
            <td style="font-size:10px; color:#8E8E93;">{{ $p->catatan_siswa ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center; padding:20px; color:#8E8E93;">Semua peminjaman sudah dikembalikan ✅</td></tr>
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
