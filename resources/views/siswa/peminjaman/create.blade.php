@extends('layouts.app')
@section('title', 'Ajukan Peminjaman')
@section('content')
<div style="max-width:640px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('siswa.katalog') }}" style="color:#007AFF; text-decoration:none; font-size:15px; display:flex; align-items:center; gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Katalog
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">Ajukan Peminjaman</div>
    </div>

    <div class="ios-card" style="padding:24px;">
        <form method="POST" action="{{ route('siswa.peminjaman.store') }}">
            @csrf
            <div style="display:grid; gap:20px;">
                {{-- Pilih Alat --}}
                <div>
                    <label class="ios-label">Pilih Alat <span style="color:#FF3B30;">*</span></label>
                    @if($selectedAlat)
                        <div style="background:rgba(0,122,255,0.06); border:1.5px solid #007AFF; border-radius:12px; padding:14px; display:flex; align-items:center; gap:14px; margin-bottom:8px;">
                            <div style="font-size:30px;">🔬</div>
                            <div>
                                <div style="font-size:15px; font-weight:700; color:#1C1C1E;">{{ $selectedAlat->nama_alat }}</div>
                                <div style="font-size:13px; color:#8E8E93;">{{ $selectedAlat->lokasi }} · Stok: {{ $selectedAlat->stok }} unit</div>
                            </div>
                        </div>
                        <input type="hidden" name="id_alat" value="{{ $selectedAlat->id_alat }}">
                        <a href="{{ route('siswa.katalog') }}" style="font-size:13px; color:#007AFF; text-decoration:none;">← Ganti alat</a>
                    @else
                        <select name="id_alat" class="ios-input ios-select" required>
                            <option value="">-- Pilih alat --</option>
                            @foreach($alat as $a)
                                <option value="{{ $a->id_alat }}" {{ old('id_alat') == $a->id_alat ? 'selected' : '' }}>
                                    {{ $a->nama_alat }} (Stok: {{ $a->stok }} | {{ $a->lokasi }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                    <div>
                        <label class="ios-label">Tanggal Pinjam <span style="color:#FF3B30;">*</span></label>
                        <input type="date" name="tanggal_pinjam" value="{{ old('tanggal_pinjam', now()->toDateString()) }}"
                               min="{{ now()->toDateString() }}" class="ios-input" required>
                    </div>
                    <div>
                        <label class="ios-label">Rencana Kembali <span style="color:#FF3B30;">*</span></label>
                        <input type="date" name="tanggal_kembali" value="{{ old('tanggal_kembali') }}"
                               min="{{ now()->addDay()->toDateString() }}" class="ios-input" required>
                    </div>
                </div>

                <div>
                    <label class="ios-label">Jumlah Unit <span style="color:#FF3B30;">*</span></label>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <button type="button" onclick="changeQty(-1)" style="width:40px; height:40px; border-radius:50%; background:#F2F2F7; border:none; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#007AFF; font-weight:700;">−</button>
                        <input type="number" id="jumlah-input" name="jumlah" value="{{ old('jumlah', 1) }}" min="1"
                               max="{{ $selectedAlat ? $selectedAlat->stok : 99 }}" class="ios-input" style="text-align:center; max-width:80px; font-size:18px; font-weight:700;" required>
                        <button type="button" onclick="changeQty(1)" style="width:40px; height:40px; border-radius:50%; background:#F2F2F7; border:none; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#007AFF; font-weight:700;">+</button>
                    </div>
                </div>

                <div>
                    <label class="ios-label">Catatan / Keperluan</label>
                    <textarea name="catatan_siswa" rows="3" class="ios-input" style="resize:vertical;"
                        placeholder="Ceritakan keperluan peminjaman (mis: praktikum biologi bab sel)">{{ old('catatan_siswa') }}</textarea>
                </div>

                {{-- Info Box --}}
                <div style="background:#F2F2F7; border-radius:12px; padding:14px;">
                    <div style="font-size:13px; font-weight:600; color:#3C3C43; margin-bottom:8px;">📋 Alur Peminjaman</div>
                    <div style="display:grid; gap:6px;">
                        @foreach(['Ajukan peminjaman','Petugas memvalidasi','Jika disetujui, ambil alat','Kembalikan tepat waktu'] as $i => $step)
                        <div style="display:flex; align-items:center; gap:10px; font-size:13px; color:#3C3C43;">
                            <div style="width:22px; height:22px; background:#007AFF; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:11px; font-weight:700; flex-shrink:0;">{{ $i+1 }}</div>
                            {{ $step }}
                        </div>
                        @endforeach
                    </div>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:8px; border-top:1px solid #F2F2F7;">
                    <a href="{{ route('siswa.katalog') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">📨 Kirim Pengajuan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function changeQty(delta) {
    const inp = document.getElementById('jumlah-input');
    const max = parseInt(inp.max) || 99;
    let val = parseInt(inp.value) + delta;
    if (val < 1) val = 1;
    if (val > max) val = max;
    inp.value = val;
}
// Auto-set min tanggal kembali saat tanggal pinjam berubah
document.querySelector('[name=tanggal_pinjam]').addEventListener('change', function() {
    const kembali = document.querySelector('[name=tanggal_kembali]');
    const d = new Date(this.value);
    d.setDate(d.getDate() + 1);
    kembali.min = d.toISOString().split('T')[0];
});
</script>
@endpush
@endsection
