@extends('layouts.app')
@section('title', 'Validasi Pengajuan')
@section('content')
<div style="max-width:1200px;">
    <div style="margin-bottom:24px;">
        <div class="page-title">📋 Daftar Pengajuan Peminjaman</div>
        <div class="page-subtitle">Validasi dan kelola pengajuan peminjaman alat dari siswa</div>
    </div>

    {{-- Filter Tabs --}}
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

    <div class="ios-card">
        <div style="overflow-x:auto;">
            <table class="ios-table">
                <thead>
                    <tr><th>#</th><th>Siswa</th><th>Alat</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Jml</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $p)
                    <tr id="row-{{ $p->id_peminjaman }}">
                        <td style="color:#8E8E93; font-size:13px;">{{ $peminjaman->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $p->siswa->nama ?? '-' }}</div>
                            <div style="font-size:12px; color:#8E8E93;">{{ $p->siswa->kelas ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $p->alat->nama_alat ?? '-' }}</div>
                            <div style="font-size:12px; color:#8E8E93;">{{ $p->alat->lokasi ?? '' }}</div>
                        </td>
                        <td style="font-size:13px; color:#3C3C43;">{{ $p->tanggal_pinjam?->format('d M Y') }}</td>
                        <td style="font-size:13px; {{ $p->status === 'acc' && $p->tanggal_kembali < now() ? 'color:#FF3B30; font-weight:600;' : 'color:#3C3C43;' }}">
                            {{ $p->tanggal_kembali?->format('d M Y') }}
                            @if($p->status === 'acc' && $p->tanggal_kembali < now())
                            <div style="font-size:11px;">⚠️ Terlambat</div>
                            @endif
                        </td>
                        <td style="font-weight:600; text-align:center;">{{ $p->jumlah }}</td>
                        <td><span class="badge badge-{{ $p->status }}">
                            @if($p->status==='pending') ⏳ Pending
                            @elseif($p->status==='acc') ✅ Disetujui
                            @elseif($p->status==='selesai') 🏁 Selesai
                            @else ❌ Ditolak @endif
                        </span></td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap; min-width:140px;">
                                @if($p->status === 'pending')
                                    {{-- ACC Button --}}
                                    <form method="POST" action="{{ route('petugas.acc', $p) }}" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-success btn-sm" onclick="return confirm('Setujui peminjaman ini? Stok akan dikurangi.')">✅ ACC</button>
                                    </form>
                                    {{-- Tolak Button --}}
                                    <button type="button" class="btn-danger btn-sm"
                                        onclick="document.getElementById('modal-tolak-{{ $p->id_peminjaman }}').style.display='flex'">
                                        ❌ Tolak
                                    </button>
                                @elseif($p->status === 'acc')
                                    <a href="{{ route('petugas.pengembalian.create', $p) }}" class="btn-primary btn-sm" style="text-decoration:none;">🔄 Input Pengembalian</a>
                                @else
                                    <span style="font-size:12px; color:#8E8E93;">—</span>
                                @endif

                                {{-- Detail --}}
                                <button type="button" class="btn-secondary btn-sm"
                                    onclick="document.getElementById('modal-detail-{{ $p->id_peminjaman }}').style.display='flex'">
                                    👁 Detail
                                </button>
                            </div>
                        </td>
                    </tr>

                    {{-- Modal Tolak --}}
                    @if($p->status === 'pending')
                    <tr>
                        <td colspan="8" style="padding:0; border:none;">
                            <div id="modal-tolak-{{ $p->id_peminjaman }}" class="modal-overlay" style="display:none;">
                                <div class="modal-box">
                                    <div style="font-size:20px; font-weight:700; color:#1C1C1E; margin-bottom:6px;">❌ Tolak Pengajuan</div>
                                    <div style="font-size:14px; color:#8E8E93; margin-bottom:16px;">Pengajuan dari <strong>{{ $p->siswa->nama }}</strong> untuk alat <strong>{{ $p->alat->nama_alat }}</strong></div>
                                    <form method="POST" action="{{ route('petugas.tolak', $p) }}">
                                        @csrf @method('PATCH')
                                        <div style="margin-bottom:16px;">
                                            <label class="ios-label">Alasan Penolakan <span style="color:#FF3B30;">*</span></label>
                                            <textarea name="catatan_petugas" rows="3" class="ios-input" placeholder="Jelaskan alasan penolakan..." required style="resize:vertical;"></textarea>
                                        </div>
                                        <div style="display:flex; gap:10px; justify-content:flex-end;">
                                            <button type="button" class="btn-secondary" onclick="document.getElementById('modal-tolak-{{ $p->id_peminjaman }}').style.display='none'">Batal</button>
                                            <button type="submit" class="btn-danger">❌ Tolak Pengajuan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endif

                    {{-- Modal Detail --}}
                    <tr>
                        <td colspan="8" style="padding:0; border:none;">
                            <div id="modal-detail-{{ $p->id_peminjaman }}" class="modal-overlay" style="display:none;" onclick="if(event.target===this)this.style.display='none'">
                                <div class="modal-box" style="max-width:520px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                                        <div style="font-size:20px; font-weight:700; color:#1C1C1E;">Detail Peminjaman</div>
                                        <button onclick="document.getElementById('modal-detail-{{ $p->id_peminjaman }}').style.display='none'" style="background:rgba(120,120,128,0.12); border:none; border-radius:50%; width:30px; height:30px; cursor:pointer; font-size:16px;">✕</button>
                                    </div>
                                    <div style="display:grid; gap:12px;">
                                        @foreach([['Siswa', $p->siswa->nama ?? '-'], ['NIS', $p->siswa->nis ?? '-'], ['Kelas', $p->siswa->kelas ?? '-'], ['Alat', $p->alat->nama_alat ?? '-'], ['Lokasi Alat', $p->alat->lokasi ?? '-'], ['Jumlah', $p->jumlah.' unit'], ['Tgl Pinjam', $p->tanggal_pinjam?->format('d M Y')], ['Rencana Kembali', $p->tanggal_kembali?->format('d M Y')], ['Status', ucfirst($p->status)]] as [$key, $val])
                                        <div style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #F2F2F7;">
                                            <span style="font-size:13px; color:#8E8E93;">{{ $key }}</span>
                                            <span style="font-size:14px; font-weight:500; color:#1C1C1E;">{{ $val }}</span>
                                        </div>
                                        @endforeach
                                        @if($p->catatan_siswa)
                                        <div style="background:#F2F2F7; border-radius:10px; padding:12px;">
                                            <div style="font-size:12px; color:#8E8E93; margin-bottom:4px;">Catatan Siswa:</div>
                                            <div style="font-size:14px; color:#1C1C1E;">{{ $p->catatan_siswa }}</div>
                                        </div>
                                        @endif
                                        @if($p->catatan_petugas)
                                        <div style="background:rgba(255,59,48,0.05); border-radius:10px; padding:12px;">
                                            <div style="font-size:12px; color:#FF3B30; margin-bottom:4px;">Catatan Petugas:</div>
                                            <div style="font-size:14px; color:#1C1C1E;">{{ $p->catatan_petugas }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center; color:#8E8E93; padding:50px;">
                        <div style="font-size:40px; margin-bottom:8px;">📭</div>
                        <div>Tidak ada data pengajuan</div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($peminjaman->hasPages())
        <div style="padding:16px; border-top:1px solid #F2F2F7;">
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
</div>
@endsection
