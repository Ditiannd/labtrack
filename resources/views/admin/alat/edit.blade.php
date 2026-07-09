@extends('layouts.app')
@section('title', 'Edit Alat')
@section('content')
<div style="max-width:600px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.alat.index') }}" style="color:#007AFF;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">Edit Alat</div>
    </div>
    <div class="ios-card" style="padding:24px;">
        <form method="POST" action="{{ route('admin.alat.update', $alat) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div style="display:grid; gap:16px;">
                <div>
                    <label class="ios-label">Nama Alat <span style="color:#FF3B30;">*</span></label>
                    <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}" class="ios-input" required>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label class="ios-label">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori', $alat->kategori) }}" class="ios-input" placeholder="Optik, Kimia, dll" list="kat-list">
                        <datalist id="kat-list">
                            @foreach($kategoriList as $k)<option value="{{ $k }}">@endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="ios-label">Lokasi</label>
                        <input type="text" name="lokasi" value="{{ old('lokasi', $alat->lokasi) }}" class="ios-input" placeholder="Lab Biologi">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label class="ios-label">Stok <span style="color:#FF3B30;">*</span></label>
                        <input type="number" name="stok" value="{{ old('stok', $alat->stok) }}" class="ios-input" min="0" required>
                    </div>
                    <div>
                        <label class="ios-label">Kondisi <span style="color:#FF3B30;">*</span></label>
                        <select name="kondisi" class="ios-input ios-select" required>
                            <option value="baik"  {{ old('kondisi',$alat->kondisi)==='baik'  ? 'selected':'' }}>✅ Baik</option>
                            <option value="rusak" {{ old('kondisi',$alat->kondisi)==='rusak' ? 'selected':'' }}>❌ Rusak</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="ios-label">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="ios-input" style="resize:vertical;">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                </div>
                <div>
                    <label class="ios-label">Foto Alat</label>
                    @if($alat->foto)
                    <div style="margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                        <img src="{{ asset('storage/'.$alat->foto) }}" style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:1px solid #F2F2F7;">
                        <span style="font-size:12px;color:#8E8E93;">Foto saat ini</span>
                    </div>
                    @endif
                    <input type="file" name="foto" accept="image/*" class="ios-input" style="padding:10px;">
                </div>
                <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid #F2F2F7;">
                    <a href="{{ route('admin.alat.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
