@extends('layouts.app')
@section('title','Data Siswa')
@section('content')
<div style="max-width:1200px;">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <div class="page-title">👨‍🎓 Data Siswa</div>
            <div class="page-subtitle">Kelola data & akun siswa — dikelompokkan per jurusan</div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.siswa.template') }}" class="btn-secondary" style="text-decoration:none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Template
            </a>
            <a href="{{ route('admin.siswa.import.form') }}" class="btn-primary" style="text-decoration:none;background:rgba(88,86,214,1);">
                📥 Import Excel
            </a>
            <a href="{{ route('admin.siswa.create') }}" class="btn-primary" style="text-decoration:none;">
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
                <input type="text" name="q" value="{{ request('q') }}" placeholder="🔍  Nama atau NIS..." class="ios-input">
            </div>
            <div style="min-width:180px;">
                <label class="ios-label">Jurusan</label>
                <select name="jurusan" class="ios-input ios-select">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusan as $j)
                    <option value="{{ $j }}" {{ request('jurusan')===$j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:160px;">
                <label class="ios-label">Kelas</label>
                <select name="kelas" class="ios-input ios-select">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                    <option value="{{ $k }}" {{ request('kelas')===$k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn-primary">Tampilkan</button>
                @if(request('q')||request('jurusan')||request('kelas'))
                <a href="{{ route('admin.siswa.index') }}" class="btn-secondary" style="text-decoration:none;">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tab mode: Tabel vs Grouped --}}
    @php $mode = request('mode','grouped'); @endphp
    <div style="display:flex;gap:8px;margin-bottom:14px;">
        <a href="{{ request()->fullUrlWithQuery(['mode'=>'grouped']) }}"
           style="text-decoration:none;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;
           {{ $mode==='grouped' ? 'background:#007AFF;color:white;' : 'background:white;color:#3C3C43;box-shadow:0 1px 3px rgba(0,0,0,0.08);' }}">
           📂 Per Jurusan
        </a>
        <a href="{{ request()->fullUrlWithQuery(['mode'=>'table']) }}"
           style="text-decoration:none;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:600;
           {{ $mode==='table' ? 'background:#007AFF;color:white;' : 'background:white;color:#3C3C43;box-shadow:0 1px 3px rgba(0,0,0,0.08);' }}">
           📋 Tabel
        </a>
    </div>

    @if($mode === 'grouped')
    {{-- ── View: Per Jurusan ── --}}
    @forelse($grouped as $jurusanNama => $siswas)
    <div class="ios-card" style="margin-bottom:14px;overflow:hidden;">
        {{-- Header Jurusan --}}
        <div style="background:linear-gradient(135deg,#007AFF,#5856D6);padding:14px 20px;display:flex;justify-content:space-between;align-items:center;cursor:pointer;"
             onclick="toggleJurusan('{{ Str::slug($jurusanNama) }}')">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:38px;height:38px;background:rgba(255,255,255,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;">🏫</div>
                <div>
                    <div style="font-size:16px;font-weight:700;color:white;">{{ $jurusanNama }}</div>
                    <div style="font-size:12px;color:rgba(255,255,255,0.7);">
                        {{ $siswas->count() }} siswa •
                        {{ $siswas->groupBy('kelas')->count() }} kelas
                    </div>
                </div>
            </div>
            <div style="display:flex;gap:8px;align-items:center;">
                @foreach($siswas->groupBy('kelas') as $kelasNama => $ks)
                <span style="background:rgba(255,255,255,0.2);color:white;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                    {{ $kelasNama }}: {{ $ks->count() }}
                </span>
                @endforeach
                <span id="arrow-{{ Str::slug($jurusanNama) }}" style="color:white;font-size:18px;transition:transform 0.2s;">▾</span>
            </div>
        </div>

        {{-- Tabel per kelas --}}
        <div id="jurusan-{{ Str::slug($jurusanNama) }}">
            @foreach($siswas->groupBy('kelas') as $kelasNama => $kelasSiswa)
            <div style="border-bottom:1px solid #F2F2F7;">
                <div style="padding:8px 20px;background:#F9F9FB;display:flex;align-items:center;gap:8px;">
                    <span style="background:#E8F2FF;color:#007AFF;padding:2px 10px;border-radius:6px;font-size:12px;font-weight:700;">{{ $kelasNama }}</span>
                    <span style="font-size:12px;color:#8E8E93;">{{ $kelasSiswa->count() }} siswa</span>
                </div>
                <table class="ios-table">
                    <thead>
                        <tr>
                            <th>No</th><th>Nama Siswa</th><th>NIS</th><th>Angkatan</th><th>Email</th><th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($kelasSiswa as $i => $s)
                        <tr>
                            <td style="color:#8E8E93;font-size:13px;">{{ $i+1 }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:30px;height:30px;background:linear-gradient(135deg,#007AFF,#5856D6);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0;">
                                        {{ strtoupper(substr($s->nama,0,1)) }}
                                    </div>
                                    <span style="font-weight:600;">{{ $s->nama }}</span>
                                </div>
                            </td>
                            <td style="font-family:monospace;color:#5856D6;font-weight:600;">{{ $s->nis }}</td>
                            <td style="color:#8E8E93;font-size:13px;">{{ $s->angkatan ?? '-' }}</td>
                            <td style="color:#8E8E93;font-size:13px;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->user->email ?? '-' }}</td>
                            <td>
                                <div style="display:flex;gap:6px;">
                                    <a href="{{ route('admin.siswa.edit',$s) }}" class="btn-secondary btn-sm" style="text-decoration:none;">✏️</a>
                                    <form method="POST" action="{{ route('admin.siswa.destroy',$s) }}" onsubmit="return confirm('Hapus siswa {{ $s->nama }}?')">
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
            @endforeach
        </div>
    </div>
    @empty
    <div class="ios-card" style="padding:60px 20px;text-align:center;">
        <div style="font-size:50px;margin-bottom:12px;">👨‍🎓</div>
        <div style="font-size:17px;font-weight:600;color:#3C3C43;margin-bottom:6px;">Belum ada data siswa</div>
        <div style="font-size:14px;color:#8E8E93;margin-bottom:20px;">Tambah manual atau import via Excel</div>
        <div style="display:flex;gap:10px;justify-content:center;">
            <a href="{{ route('admin.siswa.import.form') }}" class="btn-primary" style="text-decoration:none;">📥 Import Excel</a>
            <a href="{{ route('admin.siswa.create') }}" class="btn-secondary" style="text-decoration:none;">+ Tambah Manual</a>
        </div>
    </div>
    @endforelse

    @else
    {{-- ── View: Tabel ── --}}
    <div class="ios-card">
        <div style="overflow-x:auto;">
            <table class="ios-table">
                <thead><tr><th>#</th><th>Nama</th><th>NIS</th><th>Kelas</th><th>Jurusan</th><th>Angkatan</th><th>Email</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($siswa as $s)
                    <tr>
                        <td style="color:#8E8E93;font-size:13px;">{{ $siswa->firstItem()+$loop->index }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;background:linear-gradient(135deg,#007AFF,#5856D6);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:13px;font-weight:700;flex-shrink:0;">
                                    {{ strtoupper(substr($s->nama,0,1)) }}
                                </div>
                                <span style="font-weight:600;">{{ $s->nama }}</span>
                            </div>
                        </td>
                        <td style="font-family:monospace;color:#5856D6;font-weight:600;">{{ $s->nis }}</td>
                        <td><span style="background:#F2F2F7;padding:3px 10px;border-radius:8px;font-size:13px;">{{ $s->kelas }}</span></td>
                        <td style="font-size:13px;color:#3C3C43;">{{ $s->jurusan ?? '-' }}</td>
                        <td style="color:#8E8E93;font-size:13px;">{{ $s->angkatan ?? '-' }}</td>
                        <td style="color:#8E8E93;font-size:13px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $s->user->email ?? '-' }}</td>
                        <td>
                            <div style="display:flex;gap:6px;">
                                <a href="{{ route('admin.siswa.edit',$s) }}" class="btn-secondary btn-sm" style="text-decoration:none;">✏️ Edit</a>
                                <form method="POST" action="{{ route('admin.siswa.destroy',$s) }}" onsubmit="return confirm('Hapus siswa ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#8E8E93;padding:40px;">Belum ada data siswa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($siswa->hasPages())
        <div style="padding:16px;border-top:1px solid #F2F2F7;">
            <div class="pagination-ios">
                @if($siswa->onFirstPage())<span class="disabled">‹</span>@else<a href="{{ $siswa->previousPageUrl() }}">‹</a>@endif
                @foreach($siswa->getUrlRange(1,$siswa->lastPage()) as $page => $url)
                    @if($page==$siswa->currentPage())<span class="active">{{ $page }}</span>@else<a href="{{ $url }}">{{ $page }}</a>@endif
                @endforeach
                @if($siswa->hasMorePages())<a href="{{ $siswa->nextPageUrl() }}">›</a>@else<span class="disabled">›</span>@endif
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@push('scripts')
<script>
function toggleJurusan(slug) {
    const el = document.getElementById('jurusan-' + slug);
    const ar = document.getElementById('arrow-' + slug);
    if (el.style.display === 'none') {
        el.style.display = 'block';
        ar.style.transform = 'rotate(0deg)';
    } else {
        el.style.display = 'none';
        ar.style.transform = 'rotate(-90deg)';
    }
}
</script>
@endpush
@endsection
