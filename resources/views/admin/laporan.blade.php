@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div style="max-width:1300px;">

    {{-- Header --}}
    <div style="margin-bottom:24px;">
        <div class="page-title">📊 Dashboard Admin</div>
        <div class="page-subtitle">{{ now()->translatedFormat('l, d F Y') }} — Ringkasan sistem peminjaman laboratorium</div>
    </div>

    {{-- ── Baris 1: Stats Utama ── --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:12px; margin-bottom:20px;">
        @foreach([
            ['total_peminjaman', 'Total Peminjaman', '#007AFF', '📋'],
            ['pending',          'Menunggu Validasi', '#FF9500', '⏳'],
            ['acc',              'Sedang Dipinjam',   '#34C759', '✅'],
            ['selesai',          'Selesai',            '#5856D6', '🏁'],
            ['ditolak',          'Ditolak',            '#FF3B30', '❌'],
            ['total_alat',       'Jenis Alat',         '#1C1C1E', '🔬'],
            ['alat_rusak',       'Alat Rusak',         '#FF3B30', '⚠️'],
            ['total_siswa',      'Total Siswa',        '#007AFF', '👨‍🎓'],
        ] as [$key,$label,$color,$icon])
        <div class="ios-card" style="padding:16px 14px; text-align:center;">
            <div style="font-size:22px; margin-bottom:4px;">{{ $icon }}</div>
            <div style="font-size:28px; font-weight:800; color:{{ $color }}; line-height:1;">{{ $stats[$key] }}</div>
            <div style="font-size:11px; color:#8E8E93; margin-top:4px; font-weight:500; line-height:1.3;">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Alert Terlambat ── --}}
    @if($terlambat->count() > 0)
    <div style="background:rgba(255,59,48,0.07); border:1.5px solid rgba(255,59,48,0.25); border-radius:16px; padding:16px 20px; margin-bottom:20px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <span style="font-size:20px;">⚠️</span>
            <span style="font-size:16px; font-weight:700; color:#FF3B30;">{{ $terlambat->count() }} Peminjaman Terlambat Dikembalikan</span>
        </div>
        <div style="overflow-x:auto;">
            <table class="ios-table" style="background:white; border-radius:12px; overflow:hidden;">
                <thead><tr><th>Siswa</th><th>Alat</th><th>Rencana Kembali</th><th>Keterlambatan</th><th>Aksi</th></tr></thead>
                <tbody>
                    @foreach($terlambat as $t)
                    <tr>
                        <td style="font-weight:600;">{{ $t->siswa->nama ?? '-' }}<div style="font-size:11px;color:#8E8E93;">{{ $t->siswa->kelas ?? '' }}</div></td>
                        <td>{{ $t->alat->nama_alat ?? '-' }}</td>
                        <td style="color:#FF3B30;font-weight:600;">{{ $t->tanggal_kembali?->format('d M Y') }}</td>
                        <td><span class="badge badge-ditolak">{{ now()->diffInDays($t->tanggal_kembali) }} hari</span></td>
                        <td><a href="{{ route('petugas.pengembalian.create', $t) }}" class="btn-primary btn-sm" style="text-decoration:none;">🔄 Kembalikan</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Baris 2: Trend + Alat Populer ── --}}
    <div style="display:grid; grid-template-columns:2fr 1fr; gap:16px; margin-bottom:20px;">

        {{-- Trend 7 Hari --}}
        <div class="ios-card" style="padding:20px;">
            <div style="font-size:17px; font-weight:700; color:#1C1C1E; margin-bottom:16px;">📈 Trend Peminjaman 7 Hari Terakhir</div>
            @php $maxTrend = max(max(array_column($trendData,'total')), 1); @endphp
            <div style="display:flex; align-items:flex-end; gap:8px; height:120px;">
                @foreach($trendData as $d)
                @php $h = max(4, round(($d['total']/$maxTrend)*100)); @endphp
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; height:100%; justify-content:flex-end;">
                    <div style="font-size:11px; font-weight:700; color:#007AFF;">{{ $d['total'] > 0 ? $d['total'] : '' }}</div>
                    <div style="width:100%; background:{{ $d['total']>0 ? 'linear-gradient(180deg,#007AFF,#5856D6)' : '#F2F2F7' }}; border-radius:6px 6px 0 0; height:{{ $h }}%; transition:height 0.3s; min-height:4px;"></div>
                    <div style="font-size:10px; color:#8E8E93; white-space:nowrap;">{{ $d['label'] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Alat Populer --}}
        <div class="ios-card" style="padding:20px;">
            <div style="font-size:17px; font-weight:700; color:#1C1C1E; margin-bottom:14px;">🏆 Alat Paling Sering Dipinjam</div>
            @forelse($alat_populer as $i => $ap)
            @php $colors = ['#007AFF','#34C759','#FF9500','#5856D6','#FF3B30']; @endphp
            <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:{{ !$loop->last ? '1px solid #F2F2F7':'none' }};">
                <div style="width:26px; height:26px; background:{{ $colors[$i] }}20; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; color:{{ $colors[$i] }}; flex-shrink:0;">{{ $i+1 }}</div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:13px; font-weight:600; color:#1C1C1E; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $ap->alat->nama_alat ?? '—' }}</div>
                    <div style="font-size:11px; color:#8E8E93;">{{ $ap->total }}× dipinjam</div>
                </div>
                {{-- mini bar --}}
                @php $maxPop = $alat_populer->max('total') ?: 1; @endphp
                <div style="width:50px; height:6px; background:#F2F2F7; border-radius:3px; overflow:hidden;">
                    <div style="width:{{ round(($ap->total/$maxPop)*100) }}%; height:100%; background:{{ $colors[$i] }}; border-radius:3px;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#8E8E93; padding:20px 0; font-size:13px;">Belum ada data</div>
            @endforelse
        </div>
    </div>

    {{-- ── Baris 3: Distribusi Siswa per Jurusan + Stok per Kategori ── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:20px;">

        {{-- Per Jurusan --}}
        <div class="ios-card" style="padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div style="font-size:17px; font-weight:700; color:#1C1C1E;">👨‍🎓 Siswa per Jurusan</div>
                <a href="{{ route('admin.siswa.index') }}" style="font-size:13px; color:#007AFF; text-decoration:none; font-weight:600;">Lihat →</a>
            </div>
            @php $maxJur = $per_jurusan->max('total') ?: 1; @endphp
            @forelse($per_jurusan as $j)
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                    <span style="font-size:13px; font-weight:500; color:#1C1C1E;">{{ $j->jurusan }}</span>
                    <span style="font-size:13px; font-weight:700; color:#007AFF;">{{ $j->total }}</span>
                </div>
                <div style="height:8px; background:#F2F2F7; border-radius:4px; overflow:hidden;">
                    <div style="width:{{ round(($j->total/$maxJur)*100) }}%; height:100%; background:linear-gradient(90deg,#007AFF,#5856D6); border-radius:4px; transition:width 0.5s;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#8E8E93; padding:20px 0; font-size:13px;">
                Belum ada data jurusan<br>
                <a href="{{ route('admin.siswa.import.form') }}" style="color:#007AFF; font-size:12px;">Import siswa →</a>
            </div>
            @endforelse
        </div>

        {{-- Per Kategori Alat --}}
        <div class="ios-card" style="padding:20px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <div style="font-size:17px; font-weight:700; color:#1C1C1E;">🔬 Inventaris per Kategori</div>
                <a href="{{ route('admin.alat.index') }}" style="font-size:13px; color:#34C759; text-decoration:none; font-weight:600;">Lihat →</a>
            </div>
            @php
                $catColors = ['#34C759','#007AFF','#FF9500','#5856D6','#FF3B30','#BF5AF2','#FF6B35','#30D158'];
                $maxKat = $per_kategori->max('total_stok') ?: 1;
            @endphp
            @forelse($per_kategori as $ci => $k)
            <div style="margin-bottom:12px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                    <div>
                        <span style="font-size:13px; font-weight:500; color:#1C1C1E;">{{ $k->kategori }}</span>
                        <span style="font-size:11px; color:#8E8E93; margin-left:6px;">{{ $k->jenis }} jenis</span>
                    </div>
                    <span style="font-size:13px; font-weight:700; color:{{ $catColors[$ci % count($catColors)] }};">{{ $k->total_stok }} unit</span>
                </div>
                <div style="height:8px; background:#F2F2F7; border-radius:4px; overflow:hidden;">
                    <div style="width:{{ round(($k->total_stok/$maxKat)*100) }}%; height:100%; background:{{ $catColors[$ci % count($catColors)] }}; border-radius:4px;"></div>
                </div>
            </div>
            @empty
            <div style="text-align:center; color:#8E8E93; padding:20px 0; font-size:13px;">
                Belum ada data kategori<br>
                <a href="{{ route('admin.alat.import.form') }}" style="color:#34C759; font-size:12px;">Import alat →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ── Baris 4: Peminjaman Terbaru ── --}}
    <div class="ios-card">
        <div style="padding:18px 20px 0; display:flex; justify-content:space-between; align-items:center;">
            <div style="font-size:17px; font-weight:700; color:#1C1C1E;">📋 Peminjaman Terbaru</div>
            <a href="{{ route('petugas.daftar-pengajuan') }}" style="font-size:13px; color:#007AFF; text-decoration:none; font-weight:600;">Lihat Semua →</a>
        </div>
        <div style="padding:12px 0 0; overflow-x:auto;">
            <table class="ios-table">
                <thead>
                    <tr><th>Siswa</th><th>Alat</th><th>Jurusan</th><th>Tgl Pinjam</th><th>Rencana Kembali</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($peminjaman_terbaru as $p)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $p->siswa->nama ?? '-' }}</div>
                            <div style="font-size:11px; color:#8E8E93;">{{ $p->siswa->kelas ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:500;">{{ $p->alat->nama_alat ?? '-' }}</div>
                            <div style="font-size:11px; color:#8E8E93;">{{ $p->alat->kategori ?? '' }}</div>
                        </td>
                        <td style="font-size:12px; color:#8E8E93;">{{ $p->siswa->jurusan ?? '-' }}</td>
                        <td style="font-size:13px; color:#3C3C43;">{{ $p->tanggal_pinjam?->format('d M Y') }}</td>
                        <td style="font-size:13px; {{ $p->status==='acc' && $p->tanggal_kembali < now() ? 'color:#FF3B30;font-weight:600;' : 'color:#3C3C43;' }}">
                            {{ $p->tanggal_kembali?->format('d M Y') }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $p->status }}">
                                @if($p->status==='pending') ⏳ Pending
                                @elseif($p->status==='acc') ✅ Disetujui
                                @elseif($p->status==='selesai') 🏁 Selesai
                                @else ❌ Ditolak @endif
                            </span>
                        </td>
                        <td>
                            @if($p->status==='pending')
                                <a href="{{ route('petugas.daftar-pengajuan') }}?filter=pending" class="btn-warning btn-sm" style="text-decoration:none;">Validasi</a>
                            @elseif($p->status==='acc')
                                <a href="{{ route('petugas.pengembalian.create',$p) }}" class="btn-primary btn-sm" style="text-decoration:none;">🔄</a>
                            @else
                                <span style="font-size:12px; color:#C7C7CC;">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center; color:#8E8E93; padding:40px;">
                        <div style="font-size:36px; margin-bottom:8px;">📭</div>
                        Belum ada data peminjaman
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Baris 5: Quick Actions ── --}}
    <div style="margin-top:20px; display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px;">
        @foreach([
            [route('admin.siswa.import.form'), '📥 Import Siswa Excel', '#007AFF', 'Tambah siswa massal dari file Excel'],
            [route('admin.alat.import.form'),  '📥 Import Alat Excel',  '#34C759', 'Tambah inventaris alat dari file Excel'],
            [route('admin.alat.create'),       '➕ Tambah Alat',        '#FF9500', 'Input alat baru secara manual'],
            [route('admin.siswa.create'),      '➕ Tambah Siswa',       '#5856D6', 'Daftarkan siswa baru secara manual'],
            [route('petugas.daftar-pengajuan').'?filter=pending', '⏳ Validasi Pending ('.$stats['pending'].')', '#FF3B30', 'Tindaklanjuti pengajuan yang menunggu'],
        ] as [$url,$label,$color,$desc])
        <a href="{{ $url }}" style="text-decoration:none;" class="ios-card">
            <div style="padding:16px 18px; display:flex; align-items:center; gap:14px; transition:transform 0.15s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                <div style="width:44px; height:44px; background:{{ $color }}18; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;">
                    {{ explode(' ',$label)[0] }}
                </div>
                <div>
                    <div style="font-size:14px; font-weight:700; color:#1C1C1E;">{{ implode(' ', array_slice(explode(' ',$label),1)) }}</div>
                    <div style="font-size:12px; color:#8E8E93; margin-top:2px;">{{ $desc }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

</div>
@endsection
