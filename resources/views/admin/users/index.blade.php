@extends('layouts.app')
@section('title', 'Manajemen User')
@section('content')
<div style="max-width:1000px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px; flex-wrap:wrap; gap:12px;">
        <div>
            <div class="page-title">👤 Manajemen User</div>
            <div class="page-subtitle">Kelola akun admin dan petugas laboratorium</div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary" style="text-decoration:none;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah User
        </a>
    </div>
    <div class="ios-card">
        <div style="overflow-x:auto;">
            <table class="ios-table">
                <thead>
                    <tr><th>#</th><th>Nama</th><th>Email</th><th>Role</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                    <tr>
                        <td style="color:#8E8E93; font-size:13px;">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:34px; height:34px; background:{{ $u->role === 'admin' ? 'linear-gradient(135deg,#FF3B30,#FF6B35)' : 'linear-gradient(135deg,#34C759,#30D158)' }}; border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-size:13px; font-weight:700; flex-shrink:0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <span style="font-weight:600;">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td style="color:#8E8E93; font-size:13px;">{{ $u->email }}</td>
                        <td>
                            <span class="badge" style="{{ $u->role === 'admin' ? 'background:rgba(255,59,48,0.1); color:#FF3B30;' : 'background:rgba(52,199,89,0.1); color:#34C759;' }}">
                                {{ $u->role === 'admin' ? '👑 Admin' : '🔧 Petugas' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn-secondary btn-sm" style="text-decoration:none;">✏️ Edit</a>
                                @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger btn-sm">🗑️</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center; color:#8E8E93; padding:40px;">Belum ada data user</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
