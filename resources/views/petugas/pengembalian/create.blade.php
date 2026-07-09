@extends('layouts.app')
@section('title', 'Input Pengembalian')
@section('content')
<div style="max-width:640px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('petugas.daftar-pengajuan') }}" style="color:#007AFF; text-decoration:none; font-size:15px; display:flex; align-items:center; gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">🔄 Input Pengembalian</div>
    </div>

    {{-- Info Card --}}
    <div class="ios-card" style="padding:20px; margin-bottom:16px; background:linear-gradient(135deg,rgba(0,122,255,0.06),rgba(88,86,214,0.06));">
        <div style="font-size:13px; font-weight:600; color:#8E8E93; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.5px;">Data Peminjaman</div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
                <div style="font-size:12px; color:#8E8E93;">Siswa</div>
                <div style="font-size:15px; font-weight:600; color:#1C1C1E;">{{ $peminjaman->siswa->nama }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#8E8E93;">Kelas</div>
                <div style="font-size:15px; font-weight:600; color:#1C1C1E;">{{ $peminjaman->siswa->kelas }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#8E8E93;">Alat Dipinjam</div>
                <div style="font-size:15px; font-weight:600; color:#1C1C1E;">{{ $peminjaman->alat->nama_alat }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#8E8E93;">Jumlah</div>
                <div style="font-size:15px; font-weight:600; color:#1C1C1E;">{{ $peminjaman->jumlah }} unit</div>
            </div>
            <div>
                <div style="font-size:12px; color:#8E8E93;">Tgl Pinjam</div>
                <div style="font-size:15px; font-weight:600; color:#1C1C1E;">{{ $peminjaman->tanggal_pinjam?->format('d M Y') }}</div>
            </div>
            <div>
                <div style="font-size:12px; color:#8E8E93;">Rencana Kembali</div>
                <div style="font-size:15px; font-weight:600; color:{{ $peminjaman->tanggal_kembali < now() ? '#FF3B30' : '#1C1C1E' }};">
                    {{ $peminjaman->tanggal_kembali?->format('d M Y') }}
                    @if($peminjaman->tanggal_kembali < now())
                        <span style="font-size:12px; color:#FF3B30;"> ⚠️ Terlambat {{ now()->diffInDays($peminjaman->tanggal_kembali) }} hari</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Form Pengembalian --}}
    <div class="ios-card" style="padding:24px;">
        <div style="font-size:17px; font-weight:700; color:#1C1C1E; margin-bottom:20px;">Form Pengembalian</div>
        <form method="POST" action="{{ route('petugas.pengembalian.store', $peminjaman) }}">
            @csrf
            <div style="display:grid; gap:18px;">
                <div>
                    <label class="ios-label">Tanggal Kembali Aktual <span style="color:#FF3B30;">*</span></label>
                    <input type="date" name="tanggal_kembali_aktual" value="{{ old('tanggal_kembali_aktual', now()->toDateString()) }}" class="ios-input" required>
                </div>
                <div>
                    <label class="ios-label">Kondisi Alat Setelah Dikembalikan <span style="color:#FF3B30;">*</span></label>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <label style="cursor:pointer;">
                            <input type="radio" name="kondisi" value="baik" {{ old('kondisi','baik') === 'baik' ? 'checked' : '' }} style="display:none;" id="cond-baik">
                            <div id="card-baik" onclick="selectKondisi('baik')"
                                style="border:2px solid {{ old('kondisi','baik') === 'baik' ? '#34C759' : '#E5E5EA' }}; border-radius:14px; padding:16px; text-align:center; transition:all 0.15s; background:{{ old('kondisi','baik') === 'baik' ? 'rgba(52,199,89,0.08)' : 'white' }};">
                                <div style="font-size:28px; margin-bottom:6px;">✅</div>
                                <div style="font-size:14px; font-weight:600; color:#1C1C1E;">Baik</div>
                                <div style="font-size:12px; color:#8E8E93;">Kondisi normal</div>
                            </div>
                        </label>
                        <label style="cursor:pointer;">
                            <input type="radio" name="kondisi" value="rusak" {{ old('kondisi') === 'rusak' ? 'checked' : '' }} style="display:none;" id="cond-rusak">
                            <div id="card-rusak" onclick="selectKondisi('rusak')"
                                style="border:2px solid {{ old('kondisi') === 'rusak' ? '#FF3B30' : '#E5E5EA' }}; border-radius:14px; padding:16px; text-align:center; transition:all 0.15s; background:{{ old('kondisi') === 'rusak' ? 'rgba(255,59,48,0.08)' : 'white' }};">
                                <div style="font-size:28px; margin-bottom:6px;">⚠️</div>
                                <div style="font-size:14px; font-weight:600; color:#1C1C1E;">Rusak</div>
                                <div style="font-size:12px; color:#8E8E93;">Ada kerusakan</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Bagian kerusakan (tampil jika rusak) --}}
                <div id="kerusakan-section" style="display:{{ old('kondisi') === 'rusak' ? 'grid' : 'none' }}; gap:14px;">
                    <div style="background:rgba(255,59,48,0.06); border-radius:12px; padding:14px; border:1px solid rgba(255,59,48,0.15);">
                        <div style="font-size:13px; font-weight:600; color:#FF3B30; margin-bottom:8px;">⚠️ Detail Kerusakan</div>
                        <textarea name="deskripsi_kerusakan" rows="3" class="ios-input" placeholder="Jelaskan kerusakan yang ditemukan...">{{ old('deskripsi_kerusakan') }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="ios-label">Catatan Petugas</label>
                    <textarea name="catatan" rows="2" class="ios-input" style="resize:vertical;" placeholder="Catatan tambahan (opsional)...">{{ old('catatan') }}</textarea>
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:8px; border-top:1px solid #F2F2F7;">
                    <a href="{{ route('petugas.daftar-pengajuan') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">✅ Konfirmasi Pengembalian</button>
                </div>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function selectKondisi(val) {
    document.getElementById('cond-baik').checked = val === 'baik';
    document.getElementById('cond-rusak').checked = val === 'rusak';
    document.getElementById('card-baik').style.borderColor = val === 'baik' ? '#34C759' : '#E5E5EA';
    document.getElementById('card-baik').style.background = val === 'baik' ? 'rgba(52,199,89,0.08)' : 'white';
    document.getElementById('card-rusak').style.borderColor = val === 'rusak' ? '#FF3B30' : '#E5E5EA';
    document.getElementById('card-rusak').style.background = val === 'rusak' ? 'rgba(255,59,48,0.08)' : 'white';
    document.getElementById('kerusakan-section').style.display = val === 'rusak' ? 'grid' : 'none';
}
</script>
@endpush
@endsection
