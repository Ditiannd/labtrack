@extends('layouts.app')
@section('title','Tambah Siswa')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <a href="{{ route('admin.siswa.index') }}" style="color:#007AFF;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">Tambah Siswa</div>
    </div>
    <div class="ios-card" style="padding:24px;">
        <form method="POST" action="{{ route('admin.siswa.store') }}">
            @csrf
            <div style="display:grid;gap:16px;">
                <div style="background:#F2F2F7;border-radius:12px;padding:12px 14px;font-size:13px;color:#8E8E93;">
                    ℹ️ Akun login (email + password) akan otomatis dibuat dengan role <strong>siswa</strong>.
                </div>
                <div><label class="ios-label">Nama Lengkap <span style="color:#FF3B30;">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" class="ios-input" placeholder="Nama lengkap siswa" required></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div><label class="ios-label">NIS <span style="color:#FF3B30;">*</span></label>
                        <input type="text" name="nis" value="{{ old('nis') }}" class="ios-input" placeholder="Nomor Induk Siswa" required></div>
                    <div><label class="ios-label">Angkatan</label>
                        <input type="text" name="angkatan" value="{{ old('angkatan') }}" class="ios-input" placeholder="2024"></div>
                </div>
                <div>
                    <label class="ios-label">Jurusan</label>
                    <input type="text" name="jurusan" value="{{ old('jurusan') }}" class="ios-input" placeholder="Contoh: Rekayasa Perangkat Lunak" list="jurusan-list">
                    <datalist id="jurusan-list">
                        @foreach($jurusanList as $j)<option value="{{ $j }}">@endforeach
                    </datalist>
                </div>
                <div><label class="ios-label">Kelas <span style="color:#FF3B30;">*</span></label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}" class="ios-input" placeholder="Contoh: X RPL 1, XII IPA 2" required></div>
                <div style="height:1px;background:#F2F2F7;"></div>
                <div style="font-size:14px;font-weight:600;color:#3C3C43;">🔐 Akun Login</div>
                <div><label class="ios-label">Email <span style="color:#FF3B30;">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="ios-input" placeholder="email@sekolah.com" required></div>
                <div><label class="ios-label">Password <span style="color:#FF3B30;">*</span></label>
                    <input type="password" name="password" class="ios-input" placeholder="Min. 6 karakter" required></div>
                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #F2F2F7;">
                    <a href="{{ route('admin.siswa.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">💾 Simpan Siswa</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
