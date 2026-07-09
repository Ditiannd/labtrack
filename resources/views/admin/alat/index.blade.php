@extends('layouts.app')
@section('title','Master Alat')
@section('content')
<div style="max-width:1200px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <div class="page-title">🔬 Master Alat Laboratorium</div>
            <div class="page-subtitle">Inventaris alat dikelompokkan per kategori</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.alat.template') }}" class="btn-secondary" style="text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Template
            </a>
            <a href="{{ route('admin.alat.import.form') }}" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;background:rgba(52,199,89,1);color:white;border-radius:12px;padding:10px 18px;font-size:14px;font-weight:600;">
                📥 Import Excel
            </a>
            <a href="{{ route('admin.alat.create') }}" class="btn-primary" style="text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Manual
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="ios-card" style="padding:16px;margin-bottom:16px;">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="ios-label">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍  Nama alat atau lokasi..." class="ios-input">
            </div>
            <div style="min-width:180px;">
                <label class="ios-label">Kategori</label>
                <select name="kategori" class="ios-input ios-select">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                    <option value="{{ $k }}" {{ request('kategori')===$k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:140px;">
                <label class="ios-label">Kondisi</label>
                <select name="kondisi" class="ios-input ios-select">
                    <option value="">Semua</option>
                    <option value="baik"  {{ request('kondisi')==='baik'  ? 'selected' : '' }}>✅ Baik</option>
                    <option value="rusak" {{ request('kondisi')==='rusak' ? 'selected' : '' }}>❌ Rusak</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn-primary">Tampilkan</button>
                @if(request()->anyFilled(['q','kategori','kondisi']))
                <a href="{{ route('admin.alat.index') }}" class="btn-secondary" style="text-decoration:none;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Mode toggle --}}
    @php $mode = request('mode','grouped'); @endphp
    <div style="display:flex;gap:8px;margin-bottom:14px;">
        <a href="{{ request()->fullUrlWithQuery(['mode'=>'grouped']) }}"
           style="text-decoration:none;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;
           {{ $mode==='grouped' ? 'background:#34C759;color:white;' : 'background:white;color:#3C3C43;box-shadow:0 1px 3px rgba(0,0,0,0.08);' }}">
           📂 Per Kategori
        </a>
        <a href="{{ request()->fullUrlWithQuery(['mode'=>'table']) }}"
           style="text-decoration:none;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;
           {{ $mode==='table' ? 'background:#34C759;color:white;' : 'background:white;color:#3C3C43;box-shadow:0 1px 3px rgba(0,0,0,0.08);' }}">
           📋 Tabel
        </a>
    </div>

    @if($mode === 'grouped')
    {{-- Grouped per Kategori --}}
    @forelse($grouped as $katNama => $alatList)
    @php $colors = ['007AFF','34C759','FF9500','5856D6','FF3B30','FF6B6B','30D158','64D2FF','BF5AF2','FF9F0A']; $ci = $loop->index % count($colors); @endphp
    <div class="ios-card" style="margin-bottom:14px;overflow:hidden;">
        <div style="background:linear-gradient(135deg,#{{ $colors[$ci] }},#{{ $colors[($ci+1)%count($colors)] }});padding:14px 20px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;"
             onclick="toggleKat('{{ Str::slug($katNama) }}')">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;">🔬</div>
                <div>
                    <div style="font-size:16px;font-weight:700;color:white;">{{ $katNama }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.75);">
                        {{ $alatList->count() }} jenis alat •
                        Stok total: {{ $alatList->sum('stok') }} unit
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                <span style="background:rgba(255,255,255,0.2);color:white;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $alatList->where('kondisi','baik')->count() }} baik
                </span>
                @if($alatList->where('kondisi','rusak')->count() > 0)
                <span style="background:rgba(0,0,0,0.2);color:white;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $alatList->where('kondisi','rusak')->count() }} rusak
                </span>
                @endif
                <span id="arrow-{{ Str::slug($katNama) }}" style="color:white;font-size:18px;transition:transform 0.2s;">▾</span>
            </div>
        </div>
        <div id="kat-{{ Str::slug($katNama) }}">
            <table class="ios-table">
                <thead><tr><th>No</th><th>Nama Alat</th><th>Stok</th><th>Kondisi</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($alatList as $i => $a)
                    <tr>
                        <td style="color:#8E8E93;font-size:13px;">{{ $i+1 }}</td>
                        <td>
                            <div style="font-weight:600;color:#1C1C1E;">{{ $a->nama_alat }}</div>
                            @if($a->deskripsi)<div style="font-size:12px;color:#8E8E93;margin-top:1px;max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $a->deskripsi }}</div>@endif
                        </td>
                        <td>
                            <span style="font-size:18px;font-weight:800;color:{{ $a->stok > 0 ? '#34C759' : '#FF3B30' }};">{{ $a->stok }}</span>
                            <span style="font-size:12px;color:#8E8E93;"> unit</span>
                        </td>
                        <td><span class="badge badge-{{ $a->kondisi }}">{{ $a->kondisi === 'baik' ? '✅ Baik' : '❌ Rusak' }}</span></td>
                        <td style="color:#8E8E93;font-size:13px;">{{ $a->lokasi ?? '-' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.alat.edit',$a) }}" class="btn-secondary btn-sm" style="text-decoration:none;">✏️</a>
                                <form method="POST" action="{{ route('admin.alat.destroy',$a) }}" onsubmit="return confirm('Hapus alat {{ $a->nama_alat }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="ios-card" style="padding:60px 20px;text-align:center;">
        <div style="font-size:50px;margin-bottom:12px;">🔬</div>
        <div style="font-size:17px;font-weight:600;color:#3C3C43;margin-bottom:6px;">Belum ada data alat</div>
        <div style="font-size:14px;color:#8E8E93;margin-bottom:20px;">Tambah manual atau import via Excel</div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <a href="{{ route('admin.alat.import.form') }}" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;background:#34C759;color:white;border-radius:12px;padding:10px 18px;font-size:14px;font-weight:600;">📥 Import Excel</a>
            <a href="{{ route('admin.alat.create') }}" class="btn-secondary" style="text-decoration:none;">+ Tambah Manual</a>
        </div>
    </div>
    @endforelse

    @else
    {{-- Tabel biasa --}}
    <div class="ios-card">
        <div style="overflow-x:auto;">
            <table class="ios-table">
                <thead><tr><th>#</th><th>Nama Alat</th><th>Kategori</th><th>Stok</th><th>Kondisi</th><th>Lokasi</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($alat as $a)
                    <tr>
                        <td style="color:#8E8E93;font-size:13px;">{{ $alat->firstItem()+$loop->index }}</td>
                        <td><div style="font-weight:600;">{{ $a->nama_alat }}</div></td>
                        <td><span style="background:#F2F2F7;padding:3px 10px;border-radius:8px;font-size:12px;">{{ $a->kategori ?? '-' }}</span></td>
                        <td><span style="font-weight:700;color:{{ $a->stok > 0 ? '#34C759' : '#FF3B30' }};">{{ $a->stok }}</span></td>
                        <td><span class="badge badge-{{ $a->kondisi }}">{{ $a->kondisi }}</span></td>
                        <td style="color:#8E8E93;font-size:13px;">{{ $a->lokasi ?? '-' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.alat.edit',$a) }}" class="btn-secondary btn-sm" style="text-decoration:none;">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.alat.destroy',$a) }}" onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:#8E8E93;padding:40px;">Belum ada data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($alat->hasPages())
        <div style="padding:16px;border-top:1px solid #F2F2F7;">
            <div class="pagination-ios">
                @if($alat->onFirstPage())<span class="disabled">‹</span>@else<a href="{{ $alat->previousPageUrl() }}">‹</a>@endif
                @foreach($alat->getUrlRange(1,$alat->lastPage()) as $page => $url)
                    @if($page==$alat->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                @endforeach
                @if($alat->hasMorePages())<a href="{{ $alat->nextPageUrl() }}">›</a>@else<span class="disabled">›</span>@endif
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@push('scripts')
<script>
function toggleKat(slug) {
    const el = document.getElementById('kat-' + slug);
    const ar = document.getElementById('arrow-' + slug);
    if (el.style.display === 'none') { el.style.display='block'; ar.style.transform='rotate(0deg)'; }
    else { el.style.display='none'; ar.style.transform='rotate(-90deg)'; }
}
</script>
@endpush
@endsection
