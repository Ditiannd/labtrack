@extends('layouts.app')
@section('title','Tambah Alat')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <a href="{{ route('admin.alat.index') }}" style="color:#007AFF;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">Tambah Alat Baru</div>
    </div>
    <div class="ios-card" style="padding:24px;">
        <form method="POST" action="{{ route('admin.alat.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;gap:16px;">
                <div><label class="ios-label">Nama Alat <span style="color:#FF3B30;">*</span></label>
                    <input type="text" name="nama_alat" value="{{ old('nama_alat') }}" class="ios-input" placeholder="Contoh: Mikroskop Binokuler" required></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div><label class="ios-label">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" class="ios-input" placeholder="Optik, Kimia, dll" list="kat-list">
                        <datalist id="kat-list">@foreach($kategoriList as $k)<option value="{{ $k }}">@endforeach</datalist>
                    </div>
                    <div><label class="ios-label">Lokasi</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="ios-input" placeholder="Lab Biologi"></div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div><label class="ios-label">Stok <span style="color:#FF3B30;">*</span></label>
                        <input type="number" name="stok" value="{{ old('stok',1) }}" class="ios-input" min="0" required></div>
                    <div><label class="ios-label">Kondisi <span style="color:#FF3B30;">*</span></label>
                        <select name="kondisi" class="ios-input ios-select" required>
                            <option value="baik"  {{ old('kondisi')==='baik'  ? 'selected' : '' }}>✅ Baik</option>
                            <option value="rusak" {{ old('kondisi')==='rusak' ? 'selected' : '' }}>❌ Rusak</option>
                        </select>
                    </div>
                </div>
                <div><label class="ios-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="ios-input" style="resize:vertical;" placeholder="Keterangan singkat alat...">{{ old('deskripsi') }}</textarea></div>
                <div><label class="ios-label">Foto Alat</label>
                    <input type="file" name="foto" accept="image/*" class="ios-input" style="padding:10px;"></div>
                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #F2F2F7;">
                    <a href="{{ route('admin.alat.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">💾 Simpan Alat</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
