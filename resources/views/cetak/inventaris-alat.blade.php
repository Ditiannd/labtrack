@extends('cetak.layout')
@section('content')
<div class="laporan-header">
    <div class="laporan-header-left">
        <h1>LabTrack</h1>
        <h2>{{ $title }}</h2>
        <p>
            @if(!empty($filter['kondisi'])) Kondisi: {{ ucfirst($filter['kondisi']) }} @endif
            @if(!empty($filter['kategori'])) | Kategori: {{ $filter['kategori'] }} @endif
        </p>
    </div>
    <div class="laporan-header-right">
        <strong>Tanggal Cetak</strong>
        {{ now()->format('d F Y, H:i') }} WIB
    </div>
</div>

<div class="stats-row">
    <div class="stat-box"><div class="num">{{ $query->count() }}</div><div class="lbl">Jenis Alat</div></div>
    <div class="stat-box"><div class="num">{{ $query->sum('stok') }}</div><div class="lbl">Total Unit</div></div>
    <div class="stat-box"><div class="num" style="color:#34C759;">{{ $query->where('kondisi','baik')->count() }}</div><div class="lbl">Kondisi Baik</div></div>
    <div class="stat-box"><div class="num" style="color:#FF3B30;">{{ $query->where('kondisi','rusak')->count() }}</div><div class="lbl">Kondisi Rusak</div></div>
    <div class="stat-box"><div class="num" style="color:#34C759;">{{ $query->where('kondisi','baik')->sum('stok') }}</div><div class="lbl">Unit Tersedia</div></div>
</div>

@php $grouped = $query->groupBy('kategori'); @endphp
@foreach($grouped as $kat => $items)
<div style="margin-top:16px;">
    <div style="background:#1C1C1E; color:white; padding:6px 12px; border-radius:6px 6px 0 0; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
        {{ $kat ?: 'Tanpa Kategori' }} ({{ $items->count() }} jenis · {{ $items->sum('stok') }} unit)
    </div>
    <table style="margin-top:0;">
        <thead>
            <tr>
                <th>No</th><th>Nama Alat</th><th>Stok</th><th>Kondisi</th>
                <th>Lokasi</th><th>Deskripsi</th><th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $i => $a)
            <tr style="{{ $a->kondisi === 'rusak' ? 'background:#FFF3F3;' : '' }}">
                <td>{{ $i+1 }}</td>
                <td><strong>{{ $a->nama_alat }}</strong></td>
                <td style="text-align:center; font-size:14px; font-weight:800; color:{{ $a->stok > 0 ? '#34C759' : '#FF3B30' }};">{{ $a->stok }}</td>
                <td><span class="badge badge-{{ $a->kondisi }}">{{ strtoupper($a->kondisi) }}</span></td>
                <td>{{ $a->lokasi ?? '-' }}</td>
                <td style="font-size:10px; color:#8E8E93;">{{ Str::limit($a->deskripsi ?? '-', 50) }}</td>
                <td></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endforeach

<div class="ttd-area">
    <div class="ttd-box">
        <p>Kepala Laboratorium</p>
        <div class="ttd-line"></div>
        <p><strong>( _________________________ )</strong></p>
    </div>
</div>
@endsection
