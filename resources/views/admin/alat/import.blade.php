@extends('layouts.app')
@section('title','Import Data Alat')
@section('content')
<div style="max-width:700px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.alat.index') }}" style="color:#007AFF;text-decoration:none;font-size:15px;display:flex;align-items:center;gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">📥 Import Data Alat Lab</div>
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

    {{-- Download Template --}}
    <div class="ios-card" style="padding:18px 20px;margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:4px;">📋 Step 1 — Download Template</div>
                <div style="font-size:13px;color:#8E8E93;">Gunakan template ini agar format kolom sesuai</div>
            </div>
            <a href="{{ route('admin.alat.template') }}" class="btn-success" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;border-radius:12px;padding:10px 18px;font-size:14px;font-weight:600;color:white;">
                ⬇️ Download Template
            </a>
        </div>
    </div>

    {{-- Format kolom --}}
    <div class="ios-card" style="padding:18px 20px;margin-bottom:14px;">
        <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:12px;">📌 Step 2 — Isi Data di Template</div>
        <div style="background:#F2F2F7;border-radius:10px;padding:12px;font-size:12px;font-family:monospace;color:#1C1C1E;overflow-x:auto;margin-bottom:10px;">
            <div style="color:#8E8E93;margin-bottom:4px;"># Baris 1 = header (JANGAN diubah)</div>
            <div style="color:#007AFF;font-weight:700;">nama_alat | kategori | stok | kondisi | lokasi | deskripsi</div>
            <div style="color:#3C3C43;margin-top:4px;">Mikroskop  | Optik    | 8    | baik    | Lab Bio| Keterangan</div>
        </div>
        <div style="display:grid;gap:6px;font-size:13px;color:#3C3C43;">
            <div>✅ <strong>nama_alat</strong> — wajib diisi</div>
            <div>⚪ <strong>kondisi</strong> — isi <code style="background:#F2F2F7;padding:1px 5px;border-radius:4px;">baik</code> atau <code style="background:#F2F2F7;padding:1px 5px;border-radius:4px;">rusak</code> (default: baik)</div>
            <div>⚪ <strong>stok</strong> — angka bulat (default: 0)</div>
            <div>⚪ kolom lain boleh dikosongkan</div>
            <div style="color:#FF9500;">⚠️ Jika nama alat + lokasi sama → stok akan ditambahkan, bukan duplikat</div>
        </div>
    </div>

    {{-- Upload --}}
    <div class="ios-card" style="padding:24px;">
        <div style="font-size:15px;font-weight:700;color:#1C1C1E;margin-bottom:16px;">📤 Step 3 — Upload File</div>
        <form method="POST" action="{{ route('admin.alat.import') }}" enctype="multipart/form-data" id="importForm">
            @csrf
            <div id="dropzone"
                onclick="document.getElementById('fileInput').click()"
                ondragover="event.preventDefault();this.style.borderColor='#34C759';this.style.background='rgba(52,199,89,0.04)'"
                ondragleave="this.style.borderColor='#E5E5EA';this.style.background='#FAFAFA'"
                ondrop="handleDrop(event)"
                style="border:2px dashed #E5E5EA;border-radius:14px;padding:28px 20px;text-align:center;cursor:pointer;background:#FAFAFA;transition:all 0.2s;margin-bottom:14px;">
                <div style="font-size:36px;margin-bottom:8px;">🔬</div>
                <div style="font-size:15px;font-weight:600;color:#1C1C1E;margin-bottom:4px;">Klik atau drag & drop file Excel</div>
                <div style="font-size:13px;color:#8E8E93;">.xlsx, .xls, atau .csv — Maks. 5 MB</div>
                <div id="fileName" style="display:none;margin-top:10px;font-size:13px;color:#34C759;font-weight:600;"></div>
            </div>
            <input type="file" id="fileInput" name="file" accept=".xlsx,.xls,.csv" style="display:none;" onchange="showFile(this)">
            @error('file')<div class="alert-error" style="margin-bottom:10px;">{{ $message }}</div>@enderror
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="{{ route('admin.alat.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                <button type="submit" id="submitBtn" disabled style="opacity:0.4;background:#34C759;color:white;border:none;border-radius:12px;padding:10px 20px;font-size:15px;font-weight:600;cursor:not-allowed;display:inline-flex;align-items:center;gap:6px;">
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
