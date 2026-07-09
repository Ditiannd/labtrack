@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')
@section('content')
<div style="max-width:1000px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <div class="page-title">🕒 Riwayat Peminjaman</div>
            <div class="page-subtitle">Semua pengajuan peminjaman alat Anda</div>
        </div>
        <a href="{{ route('siswa.peminjaman.create') }}" class="btn-primary" style="text-decoration:none;">
            + Ajukan Baru
        </a>
    </div>

    {{-- Filter status --}}
    <div style="display:flex; gap:8px; margin-bottom:16px; overflow-x:auto; padding-bottom:4px;">
        @php $filter = request('filter', 'semua'); @endphp
        @foreach(['semua'=>'Semua','pending'=>'⏳ Pending','acc'=>'✅ Disetujui','selesai'=>'🏁 Selesai','ditolak'=>'❌ Ditolak'] as $val => $label)
        <a href="?filter={{ $val }}"
           style="text-decoration:none; white-space:nowrap; padding:8px 16px; border-radius:20px; font-size:13px; font-weight:600; transition:all 0.15s;
           {{ $filter === $val ? 'background:#007AFF; color:white;' : 'background:white; color:#3C3C43; box-shadow:0 1px 3px rgba(0,0,0,0.08);' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    @forelse($peminjaman as $p)
    <div class="ios-card" style="margin-bottom:12px; padding:0; overflow:hidden;">
        <div style="padding:16px 18px; display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; gap:14px; align-items:flex-start;">
                <div style="width:46px; height:46px; border-radius:12px; background:linear-gradient(135deg,#F0F8FF,#E0EEFF); display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">🔬</div>
                <div>
                    <div style="font-size:16px; font-weight:700; color:#1C1C1E;">{{ $p->alat->nama_alat ?? 'Alat dihapus' }}</div>
                    <div style="font-size:13px; color:#8E8E93; margin-top:2px;">{{ $p->alat->lokasi ?? '' }}</div>
                    <div style="margin-top:8px; display:flex; flex-wrap:wrap; gap:8px;">
                        <span style="font-size:12px; background:#F2F2F7; color:#3C3C43; padding:3px 10px; border-radius:8px; font-weight:500;">
                            📅 {{ $p->tanggal_pinjam?->format('d M Y') }} → {{ $p->tanggal_kembali?->format('d M Y') }}
                        </span>
                        <span style="font-size:12px; background:#F2F2F7; color:#3C3C43; padding:3px 10px; border-radius:8px; font-weight:500;">
                            📦 {{ $p->jumlah }} unit
                        </span>
                        @if($p->status === 'acc' && $p->tanggal_kembali < now())
                        <span style="font-size:12px; background:rgba(255,59,48,0.1); color:#FF3B30; padding:3px 10px; border-radius:8px; font-weight:600;">
                            ⚠️ Terlambat {{ now()->diffInDays($p->tanggal_kembali) }} hari
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                <span class="badge badge-{{ $p->status }}">
                    @if($p->status==='pending') ⏳ Menunggu Validasi
                    @elseif($p->status==='acc') ✅ Disetujui
                    @elseif($p->status==='selesai') 🏁 Selesai
                    @else ❌ Ditolak @endif
                </span>
                <span style="font-size:12px; color:#C7C7CC;">{{ $p->created_at?->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Keterangan / catatan --}}
        @if($p->catatan_siswa || $p->catatan_petugas)
        <div style="padding:12px 18px; background:#F9F9F9; border-top:1px solid #F0F0F0; display:flex; flex-wrap:wrap; gap:12px;">
            @if($p->catatan_siswa)
            <div style="flex:1; min-width:180px;">
                <div style="font-size:11px; font-weight:600; color:#8E8E93; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px;">Catatan Anda</div>
                <div style="font-size:13px; color:#3C3C43;">{{ $p->catatan_siswa }}</div>
            </div>
            @endif
            @if($p->catatan_petugas)
            <div style="flex:1; min-width:180px;">
                <div style="font-size:11px; font-weight:600; color:{{ $p->status === 'ditolak' ? '#FF3B30' : '#8E8E93' }}; text-transform:uppercase; letter-spacing:0.4px; margin-bottom:3px;">
                    {{ $p->status === 'ditolak' ? '❌ Alasan Penolakan' : 'Catatan Petugas' }}
                </div>
                <div style="font-size:13px; color:{{ $p->status === 'ditolak' ? '#FF3B30' : '#3C3C43' }};">{{ $p->catatan_petugas }}</div>
            </div>
            @endif
        </div>
        @endif

        {{-- Pengembalian info jika selesai --}}
        @if($p->status === 'selesai' && $p->pengembalian)
        <div style="padding:10px 18px; background:rgba(52,199,89,0.05); border-top:1px solid rgba(52,199,89,0.15); display:flex; gap:16px; flex-wrap:wrap;">
            <span style="font-size:12px; color:#34C759; font-weight:600;">
                🏁 Dikembalikan: {{ $p->pengembalian->tanggal_kembali_aktual?->format('d M Y') }}
            </span>
            <span style="font-size:12px; color:#8E8E93;">
                Kondisi: {{ ucfirst($p->pengembalian->kondisi) }}
            </span>
        </div>
        @endif
    </div>
    @empty
    <div class="ios-card" style="padding:60px 20px; text-align:center;">
        <div style="font-size:50px; margin-bottom:12px;">📋</div>
        <div style="font-size:17px; font-weight:600; color:#3C3C43; margin-bottom:6px;">Belum ada riwayat</div>
        <div style="font-size:14px; color:#8E8E93; margin-bottom:20px;">Mulai ajukan peminjaman alat praktikum</div>
        <a href="{{ route('siswa.peminjaman.create') }}" class="btn-primary" style="text-decoration:none; display:inline-flex;">+ Ajukan Sekarang</a>
    </div>
    @endforelse

    @if($peminjaman->hasPages())
    <div style="margin-top:16px;">
        <div class="pagination-ios">
            @if($peminjaman->onFirstPage())<span class="disabled">‹</span>@else<a href="{{ $peminjaman->previousPageUrl().'&filter='.$filter }}">‹</a>@endif
            @foreach($peminjaman->getUrlRange(1, $peminjaman->lastPage()) as $page => $url)
                @if($page == $peminjaman->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url.'&filter='.$filter }}">{{ $page }}</a>@endif
            @endforeach
            @if($peminjaman->hasMorePages())<a href="{{ $peminjaman->nextPageUrl().'&filter='.$filter }}">›</a>@else<span class="disabled">›</span>@endif
        </div>
    </div>
    @endif
</div>
@endsection
