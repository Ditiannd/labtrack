{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lab System') — LabTrack</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-font-smoothing: antialiased; }
        body { background: #F2F2F7; font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', sans-serif; }

        /* iOS Sidebar */
        .sidebar { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-right: 1px solid rgba(0,0,0,0.08); }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 12px; font-size: 14px; font-weight: 500; color: #3C3C43; transition: all 0.15s ease; text-decoration: none; margin-bottom: 2px; }
        .nav-item:hover { background: rgba(120,120,128,0.12); }
        .nav-item.active { background: #007AFF; color: white; }
        .nav-item .nav-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
        .nav-item.active .nav-icon { background: rgba(255,255,255,0.2); }
        .nav-item:not(.active) .nav-icon { background: rgba(120,120,128,0.1); }

        /* iOS Cards */
        .ios-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 4px 20px rgba(0,0,0,0.04); overflow: hidden; }
        .ios-card-inset { background: #F2F2F7; border-radius: 12px; }

        /* iOS Buttons */
        .btn-primary { background: #007AFF; color: white; border: none; border-radius: 12px; padding: 10px 20px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-primary:hover { background: #0056CC; transform: translateY(-1px); }
        .btn-primary:active { transform: scale(0.97); }
        .btn-secondary { background: rgba(120,120,128,0.12); color: #3C3C43; border: none; border-radius: 12px; padding: 10px 20px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-secondary:hover { background: rgba(120,120,128,0.2); }
        .btn-danger { background: #FF3B30; color: white; border: none; border-radius: 12px; padding: 8px 16px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.15s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-danger:hover { background: #CC2222; }
        .btn-success { background: #34C759; color: white; border: none; border-radius: 12px; padding: 8px 16px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
        .btn-success:hover { background: #27A048; }
        .btn-warning { background: #FF9500; color: white; border: none; border-radius: 12px; padding: 8px 16px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.15s; }
        .btn-sm { padding: 6px 12px; font-size: 13px; border-radius: 10px; }

        /* iOS Form Inputs */
        .ios-input { width: 100%; background: #F2F2F7; border: 1.5px solid transparent; border-radius: 12px; padding: 12px 14px; font-size: 15px; color: #1C1C1E; transition: all 0.15s; outline: none; }
        .ios-input:focus { background: white; border-color: #007AFF; box-shadow: 0 0 0 3px rgba(0,122,255,0.1); }
        .ios-label { font-size: 13px; font-weight: 600; color: #3C3C43; margin-bottom: 6px; display: block; }
        .ios-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238E8E93' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 40px; }

        /* iOS Table */
        .ios-table { width: 100%; border-collapse: separate; border-spacing: 0; }
        .ios-table thead th { font-size: 12px; font-weight: 600; color: #8E8E93; text-transform: uppercase; letter-spacing: 0.5px; padding: 12px 16px; background: #F2F2F7; }
        .ios-table thead th:first-child { border-radius: 12px 0 0 0; }
        .ios-table thead th:last-child { border-radius: 0 12px 0 0; }
        .ios-table tbody td { padding: 14px 16px; border-bottom: 1px solid rgba(0,0,0,0.05); font-size: 14px; color: #1C1C1E; }
        .ios-table tbody tr:last-child td { border-bottom: none; }
        .ios-table tbody tr:hover td { background: rgba(0,122,255,0.03); }

        /* iOS Badge */
        .badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: rgba(255,149,0,0.15); color: #FF9500; }
        .badge-acc { background: rgba(52,199,89,0.15); color: #34C759; }
        .badge-selesai { background: rgba(0,122,255,0.15); color: #007AFF; }
        .badge-ditolak { background: rgba(255,59,48,0.15); color: #FF3B30; }
        .badge-baik { background: rgba(52,199,89,0.15); color: #34C759; }
        .badge-rusak { background: rgba(255,59,48,0.15); color: #FF3B30; }

        /* iOS Top Bar */
        .topbar { background: rgba(255,255,255,0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-bottom: 1px solid rgba(0,0,0,0.08); height: 60px; display: flex; align-items: center; padding: 0 20px; position: sticky; top: 0; z-index: 100; }

        /* Stats Card */
        .stat-card { background: white; border-radius: 16px; padding: 20px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        .stat-number { font-size: 32px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 12px; color: #8E8E93; margin-top: 4px; font-weight: 500; }

        /* Flash Messages */
        .alert-success { background: rgba(52,199,89,0.1); border: 1px solid rgba(52,199,89,0.3); color: #1D6435; border-radius: 12px; padding: 12px 16px; font-size: 14px; }
        .alert-error { background: rgba(255,59,48,0.1); border: 1px solid rgba(255,59,48,0.3); color: #CC2222; border-radius: 12px; padding: 12px 16px; font-size: 14px; }

        /* Page Title */
        .page-title { font-size: 28px; font-weight: 700; color: #1C1C1E; letter-spacing: -0.5px; }
        .page-subtitle { font-size: 14px; color: #8E8E93; margin-top: 2px; }

        /* Divider */
        .section-header { font-size: 13px; font-weight: 600; color: #8E8E93; text-transform: uppercase; letter-spacing: 0.5px; padding: 0 4px; margin-bottom: 6px; margin-top: 16px; }

        /* Modal Overlay */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(4px); z-index: 999; display: flex; align-items: center; justify-content: center; }
        .modal-box { background: white; border-radius: 20px; padding: 24px; width: 90%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }

        /* Pagination */
        .pagination-ios { display: flex; gap: 6px; align-items: center; justify-content: center; }
        .pagination-ios span, .pagination-ios a { min-width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-size: 14px; font-weight: 500; }
        .pagination-ios a { background: white; color: #007AFF; text-decoration: none; transition: all 0.15s; }
        .pagination-ios a:hover { background: #007AFF; color: white; }
        .pagination-ios span.active { background: #007AFF; color: white; }
        .pagination-ios span.disabled { color: #C7C7CC; }
    </style>
</head>
<body>
    {{-- Top Bar --}}
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:12px; flex:1;">
            {{-- Mobile Menu Toggle --}}
            <button id="sidebarToggle" style="display:none; background:none; border:none; cursor:pointer; padding:4px;" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#007AFF" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:30px; height:30px; background:linear-gradient(135deg,#007AFF,#5856D6); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                </div>
                <span style="font-size:17px; font-weight:700; color:#1C1C1E; letter-spacing:-0.3px;">LabTrack</span>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="text-align:right;">
                <div style="font-size:14px; font-weight:600; color:#1C1C1E;">{{ Auth::user()->name }}</div>
                <div style="font-size:11px; color:#8E8E93; text-transform:capitalize;">{{ Auth::user()->role }}</div>
            </div>
            <div style="width:36px; height:36px; background:linear-gradient(135deg,#007AFF,#5856D6); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:14px;">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button style="background:rgba(255,59,48,0.1); color:#FF3B30; border:none; border-radius:10px; padding:7px 14px; font-size:13px; font-weight:600; cursor:pointer;">Keluar</button>
            </form>
        </div>
    </div>

    {{-- Layout --}}
    <div style="display:flex; min-height: calc(100vh - 60px);">
        {{-- Sidebar --}}
        <aside id="sidebar" class="sidebar" style="width:240px; flex-shrink:0; padding:16px 12px; overflow-y:auto;">
            @include('partials.sidebar')
        </aside>

        {{-- Main --}}
        <main style="flex:1; padding:24px; overflow-x:hidden; min-width:0;">
            @if(session('success'))
                <div class="alert-success" style="margin-bottom:16px;">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert-error" style="margin-bottom:16px;">
                    <ul style="margin:0; padding-left:16px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    <style>
        @media (max-width: 768px) {
            #sidebarToggle { display:flex !important; }
            #sidebar { position:fixed; left:0; top:60px; bottom:0; z-index:200; transform:translateX(-100%); transition:transform 0.3s ease; }
            #sidebar.open { transform:translateX(0); box-shadow:4px 0 20px rgba(0,0,0,0.2); }
        }
    </style>
    @stack('scripts')
</body>
</html>
