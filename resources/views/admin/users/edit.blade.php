@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div style="max-width:560px;">
    <div style="margin-bottom:24px; display:flex; align-items:center; gap:12px;">
        <a href="{{ route('admin.users.index') }}" style="color:#007AFF; text-decoration:none; font-size:15px; display:flex; align-items:center; gap:4px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg> Kembali
        </a>
        <div style="color:#C7C7CC;">|</div>
        <div class="page-title" style="font-size:22px;">Edit User</div>
    </div>
    <div class="ios-card" style="padding:24px;">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PUT')
            <div style="display:grid; gap:18px;">
                <div>
                    <label class="ios-label">Nama Lengkap <span style="color:#FF3B30;">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="ios-input" required>
                </div>
                <div>
                    <label class="ios-label">Email <span style="color:#FF3B30;">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="ios-input" required>
                </div>
                <div>
                    <label class="ios-label">Password Baru <span style="color:#8E8E93; font-weight:400;">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="ios-input" placeholder="Min. 6 karakter">
                </div>
                <div>
                    <label class="ios-label">Role <span style="color:#FF3B30;">*</span></label>
                    <select name="role" class="ios-input ios-select" required>
                        <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>🔧 Petugas</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>👑 Admin</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:8px; border-top:1px solid #F2F2F7;">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary" style="text-decoration:none;">Batal</a>
                    <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
