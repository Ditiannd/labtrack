@php
$role = auth()->user()->role;
$pendingCount   = \App\Models\Peminjaman::where('status','pending')->count();
$terlambatCount = \App\Models\Peminjaman::where('status','acc')->where('tanggal_kembali','<',now()->toDateString())->count();
@endphp

@if($role === 'admin')
    <div class="section-header">Admin</div>
    <a href="{{ route('admin.laporan') }}" class="nav-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}">
        <span class="nav-icon">📊</span> Dashboard
    </a>
    <a href="{{ route('admin.alat.index') }}" class="nav-item {{ request()->routeIs('admin.alat.*') ? 'active' : '' }}">
        <span class="nav-icon">🔬</span> Master Alat
    </a>
    <a href="{{ route('admin.siswa.index') }}" class="nav-item {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
        <span class="nav-icon">👨‍🎓</span> Data Siswa
    </a>
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
        <span class="nav-icon">👤</span> Manajemen User
    </a>

    <div style="height:1px;background:rgba(0,0,0,0.06);margin:12px 4px;"></div>
    <div class="section-header">Operasional</div>

    <a href="{{ route('petugas.daftar-pengajuan') }}" class="nav-item {{ request()->routeIs('petugas.daftar-pengajuan') && !request()->routeIs('cetak.*') ? 'active' : '' }}"
       style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;"><span class="nav-icon">📋</span> Validasi Pengajuan</div>
        @if($pendingCount > 0)
        <span style="background:#FF3B30;color:white;font-size:11px;font-weight:700;min-width:20px;height:20px;border-radius:10px;display:flex;align-items:center;justify-content:center;padding:0 5px;">{{ $pendingCount }}</span>
        @endif
    </a>

    @if($terlambatCount > 0)
    <a href="{{ route('cetak.terlambat') }}" target="_blank" style="display:flex;align-items:center;gap:8px;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:600;color:#FF3B30;text-decoration:none;background:rgba(255,59,48,0.07);margin-bottom:2px;">
        <span>⚠️</span> {{ $terlambatCount }} Terlambat
    </a>
    @endif

    <div style="height:1px;background:rgba(0,0,0,0.06);margin:12px 4px;"></div>
    <div class="section-header">Laporan & Cetak</div>

    <a href="{{ route('cetak.index') }}" class="nav-item {{ request()->routeIs('cetak.*') ? 'active' : '' }}">
        <span class="nav-icon">🖨️</span> Cetak Laporan
    </a>
@endif

@if($role === 'petugas')
    <div class="section-header">Petugas Lab</div>
    <a href="{{ route('petugas.daftar-pengajuan') }}"
       class="nav-item {{ request()->routeIs('petugas.daftar-pengajuan') && !request('filter') ? 'active' : '' }}"
       style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;"><span class="nav-icon">📋</span> Semua Pengajuan</div>
    </a>
    <a href="{{ route('petugas.daftar-pengajuan') }}?filter=pending"
       class="nav-item {{ request('filter')==='pending' ? 'active' : '' }}"
       style="justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:10px;"><span class="nav-icon">⏳</span> Perlu Validasi</div>
        @if($pendingCount > 0)
        <span style="background:#FF9500;color:white;font-size:11px;font-weight:700;min-width:20px;height:20px;border-radius:10px;display:flex;align-items:center;justify-content:center;padding:0 5px;">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('petugas.daftar-pengajuan') }}?filter=acc"
       class="nav-item {{ request('filter')==='acc' ? 'active' : '' }}">
        <span class="nav-icon">🔄</span> Perlu Pengembalian
    </a>
    <a href="{{ route('petugas.daftar-pengajuan') }}?filter=selesai"
       class="nav-item {{ request('filter')==='selesai' ? 'active' : '' }}">
        <span class="nav-icon">🏁</span> Selesai
    </a>

    <div style="height:1px;background:rgba(0,0,0,0.06);margin:12px 4px;"></div>
    <div class="section-header">Laporan & Cetak</div>
    <a href="{{ route('cetak.index') }}" class="nav-item {{ request()->routeIs('cetak.*') ? 'active' : '' }}">
        <span class="nav-icon">🖨️</span> Cetak Laporan
    </a>
@endif

@if($role === 'siswa')
    <div class="section-header">Siswa</div>
    <a href="{{ route('siswa.katalog') }}" class="nav-item {{ request()->routeIs('siswa.katalog') ? 'active' : '' }}">
        <span class="nav-icon">🔬</span> Katalog Alat
    </a>
    <a href="{{ route('siswa.peminjaman.create') }}" class="nav-item {{ request()->routeIs('siswa.peminjaman.create') ? 'active' : '' }}">
        <span class="nav-icon">➕</span> Ajukan Peminjaman
    </a>
    <a href="{{ route('siswa.riwayat') }}" class="nav-item {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}">
        <span class="nav-icon">🕒</span> Riwayat Peminjaman
    </a>
@endif
