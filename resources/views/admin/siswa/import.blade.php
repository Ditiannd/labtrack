@extends('layouts.app')
@section('title','Import Data Siswa')
@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.siswa.index') }}" style="color:#007AFF;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">📥 Import Data Siswa</div>
    </div>

    @if(session('error'))
    <div class="alert-error" style="margin-bottom:16px;">❌ {{ session('error') }}</div>
    @endif

    @if(session('import_errors') && count(session('import_errors')) > 0)
    <div style="background:rgba(255,149,0,0.08);border:1.5px solid rgba(255,149,0,0.3);border-radius:14px;padding:16px;margin-bottom:16px;">
        <div style="font-size:14px;font-weight:700;color:#FF9500;margin-bottom:8px;">⚠️ Catatan Import</div>
        <ul style="margin:0;padding-left:16px;display:grid;gap:4px;">
            @foreach(session('import_errors') as $err)
            <li style="font-size:13px;color:#3C3C43;">{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="ios-card" style="padding:18px 20px;margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:4px;">📋 Step 1 — Download Template</div>
                <div style="font-size:13px;color:#8E8E93;">Gunakan template ini agar format kolom sesuai</div>
            </div>
            <a href="{{ route('admin.siswa.template') }}" class="btn-primary" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;border-radius:12px;padding:10px 18px;font-size:14px;font-weight:600;color:white;">
                ⬇️ Download Template
            </a>
        </div>
    </div>

    <div class="ios-card" style="padding:18px 20px;margin-bottom:14px;">
        <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:12px;">📌 Step 2 — Isi Data di Template</div>
        <div style="background:#F2F2F7;border-radius:10px;padding:12px;font-size:12px;font-family:monospace;color:#1C1C1E;overflow-x:auto;margin-bottom:10px;">
            <div style="color:#8E8E93;margin-bottom:4px;"># Baris 1 = header (JANGAN diubah)</div>
            <div style="color:#007AFF;font-weight:700;">nis | nama | kelas | jurusan | angkatan | email | password</div>
            <div style="color:#3C3C43;margin-top:4px;">2024001 | Ahmad | X RPL 1 | RPL | 2024 | (kosong) | (kosong)</div>
        </div>
        <div style="display:grid;gap:6px;font-size:13px;color:#3C3C43;">
            <div>✅ <strong>nis</strong> dan <strong>nama</strong> — wajib diisi</div>
            <div>⚪ <strong>email</strong> — jika kosong, otomatis dibuat dari nama+NIS</div>
            <div>⚪ <strong>password</strong> — jika kosong, default = NIS siswa</div>
            <div>⚠️ <strong style="color:#FF9500;">NIS harus unik</strong> — siswa dengan NIS yang sudah ada akan dilewati</div>
            <div style="color:#8E8E93;">💡 Format NIS: pastikan kolom NIS di Excel diformat sebagai <strong>Teks</strong>, bukan angka</div>
        </div>
    </div>

    <div class="ios-card" style="padding:24px;">
        <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:16px;">📤 Step 3 — Upload File</div>
        <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data" id="importForm">
            @csrf
            <div id="dropzone"
                onclick="document.getElementById('fileInput').click()"
                ondragover="event.preventDefault();this.style.borderColor='#007AFF';this.style.background='rgba(0,122,255,0.04)'"
                ondragleave="this.style.borderColor='#E5E5EA';this.style.background='#FAFAFA'"
                ondrop="handleDrop(event)"
                style="border:2px dashed #E5E5EA;border-radius:14px;padding:28px 20px;text-align:center;cursor:pointer;background:#FAFAFA;transition:all 0.2s;margin-bottom:14px;">
                <div style="font-size:36px;margin-bottom:8px;">👨‍🎓</div>
                <div style="font-size:15px;font-weight:600;color:#1C1C1E;margin-bottom:4px;">Klik atau drag & drop file Excel</div>
                <div style="font-size:13px;color:#8E8E93;">.xlsx, .xls, atau .csv — Maks. 5 MB</div>
                <div id="fileName" style="display:none;margin-top:10px;font-size:13px;color:#007AFF;font-weight:600;"></div>
            </div>
            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" style="display:none;" onchange="showFile(this)">
            @error('file')<div class="alert-error" style="margin-bottom:10px;">{{ $message }}</div>@enderror
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('admin.siswa.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                <button type="submit" id="submitBtn" disabled style="opacity:0.4;background:#007AFF;color:white;border:none;border-radius:12px;padding:10px 20px;font-size:15px;font-weight:600;cursor:not-allowed;display:inline-flex;align-items:center;gap:6px;">
                    📥 Import Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function showFile(input) {
    const btn = document.getElementById('submitBtn');
    const div = document.getElementById('fileName');
    if (input.files && input.files.length > 0) {
        div.textContent = '✅ ' + input.files[0].name;
        div.style.display = 'block';
        btn.disabled = false;
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    }
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropzone').style.borderColor = '#E5E5EA';
    const dt = new DataTransfer();
    if (e.dataTransfer.files[0]) {
        dt.items.add(e.dataTransfer.files[0]);
        const fi = document.getElementById('fileInput');
        fi.files = dt.files;
        showFile(fi);
    }
}
document.getElementById('importForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    btn.textContent = '⏳ Sedang memproses...';
    btn.disabled = true;
});
</script>
@endpush
@endsection
