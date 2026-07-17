@extends('layouts.app')
@section('title', 'Katalog Alat')
@section('content')
<div style="max-width:1100px;">
    <div style="margin-bottom:24px;">
        <div class="page-title">🔬 Katalog Alat Praktikum</div>
        <div class="page-subtitle">Lihat ketersediaan alat sebelum mengajukan peminjaman</div>
    </div>

    {{-- Search & Filter --}}
    <div class="ios-card" style="padding:16px; margin-bottom:20px;">
        <form method="GET" style="display:flex; gap:12px; flex-wrap:wrap;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍  Cari nama alat..." class="ios-input" style="max-width:320px;">
            <select name="kondisi" class="ios-input ios-select" style="max-width:200px;">
                <option value="">Semua Kondisi</option>
                <option value="baik" {{ request('kondisi')==='baik' ? 'selected' : '' }}>✅ Baik</option>
                <option value="rusak" {{ request('kondisi')==='rusak' ? 'selected' : '' }}>❌ Rusak</option>
            </select>
            <button type="submit" class="btn-primary" style="white-space:nowrap;">Cari</button>
            @if(request('q') || request('kondisi'))
            <a href="{{ route('siswa.katalog') }}" class="btn-secondary" style="text-decoration:none; white-space:nowrap;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Grid Alat --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:14px;">
        @forelse($alat as $a)
        <div class="ios-card" style="padding:0; overflow:hidden; display:flex; flex-direction:column;">
            {{-- Gambar / Placeholder --}}
            <div style="height:140px; background:linear-gradient(135deg,
                {{ $a->kondisi === 'baik' ? '#F0F8FF,#E8F0FF' : '#FFF0F0,#FFE8E8' }});
                display:flex; align-items:center; justify-content:center; font-size:52px; position:relative;">
                @if($a->foto)
                    <img src="{{ asset('storage/'.$a->foto) }}" style="width:100%; height:100%; object-fit:cover;">
                @else
                    🔬
                @endif
                <div style="position:absolute; top:10px; right:10px;">
                    <span class="badge badge-{{ $a->kondisi }}">{{ $a->kondisi === 'baik' ? '✅ Baik' : '❌ Rusak' }}</span>
                </div>
            </div>

            <div style="padding:16px; flex:1; display:flex; flex-direction:column;">
                <div style="font-size:16px; font-weight:700; color:#1C1C1E; margin-bottom:4px;">{{ $a->nama_alat }}</div>
                @if($a->deskripsi)
                <div style="font-size:13px; color:#8E8E93; margin-bottom:8px; line-height:1.4;">{{ Str::limit($a->deskripsi, 80) }}</div>
                @endif
                <div style="display:flex; gap:12px; margin-bottom:12px; flex-wrap:wrap;">
                    <div style="font-size:13px; color:#8E8E93; display:flex; align-items:center; gap:4px;">
                        📍 {{ $a->lokasi ?? 'Tidak ditentukan' }}
                    </div>
                </div>
                <div style="display:flex; align-items:center; justify-content:space-between; margin-top:auto;">
                    <div>
                        <span style="font-size:22px; font-weight:800; color:{{ $a->stok > 0 ? '#34C759' : '#FF3B30' }};">{{ $a->stok }}</span>
                        <span style="font-size:13px; color:#8E8E93;"> unit tersedia</span>
                    </div>
                    @if($a->stok > 0 && $a->kondisi === 'baik')
                        <a href="{{ route('siswa.peminjaman.create', ['alat_id' => $a->id_alat]) }}"
                           class="btn-primary btn-sm" style="text-decoration:none;">+ Pinjam</a>
                    @else
                        <span style="font-size:12px; color:#FF3B30; font-weight:600; background:rgba(255,59,48,0.08); padding:6px 12px; border-radius:10px;">
                            {{ $a->kondisi === 'rusak' ? 'Rusak' : 'Habis' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1; text-align:center; padding:60px 20px; color:#8E8E93;">
            <div style="font-size:50px; margin-bottom:12px;">🔍</div>
            <div style="font-size:17px; font-weight:600; color:#3C3C43; margin-bottom:4px;">Tidak ditemukan</div>
            <div style="font-size:14px;">Coba ubah kata kunci pencarian</div>
        </div>
        @endforelse
    </div>

    @if($alat->hasPages())
    <div style="margin-top:20px;">
        <div class="pagination-ios">
            @if($alat->onFirstPage())<span class="disabled">‹</span>@else<a href="{{ $alat->previousPageUrl() }}">‹</a>@endif
            @foreach($alat->getUrlRange(1, $alat->lastPage()) as $page => $url)
                @if($page == $alat->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
            @endforeach
            @if($alat->hasMorePages())<a href="{{ $alat->nextPageUrl() }}">›</a>@else<span class="disabled">›</span>@endif
        </div>
    </div>
    @endif
</div>
@endsection
