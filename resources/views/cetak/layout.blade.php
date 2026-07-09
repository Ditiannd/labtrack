<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Laporan' }} — LabTrack</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1C1C1E; background: white; }

        .print-wrap { max-width: 960px; margin: 0 auto; padding: 20px; }

        /* Header laporan */
        .laporan-header { border-bottom: 3px solid #007AFF; padding-bottom: 14px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; }
        .laporan-header-left h1 { font-size: 18px; font-weight: 700; color: #007AFF; }
        .laporan-header-left h2 { font-size: 13px; font-weight: 600; color: #3C3C43; margin-top: 2px; }
        .laporan-header-left p  { font-size: 11px; color: #8E8E93; margin-top: 4px; }
        .laporan-header-right   { text-align: right; font-size: 11px; color: #8E8E93; }
        .laporan-header-right strong { display: block; font-size: 13px; color: #1C1C1E; }

        /* Tabel */
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        thead th { background: #007AFF; color: white; padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #E5E5EA; font-size: 11px; vertical-align: top; }
        tbody tr:nth-child(even) td { background: #F9F9FB; }
        tbody tr:last-child td { border-bottom: 2px solid #007AFF; }

        /* Badge status */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; }
        .badge-pending  { background: #FFF3E0; color: #E65100; }
        .badge-acc      { background: #E8F5E9; color: #1B5E20; }
        .badge-selesai  { background: #E3F2FD; color: #0D47A1; }
        .badge-ditolak  { background: #FFEBEE; color: #B71C1C; }
        .badge-baik     { background: #E8F5E9; color: #1B5E20; }
        .badge-rusak    { background: #FFEBEE; color: #B71C1C; }

        /* Stats summary */
        .stats-row { display: flex; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .stat-box { background: #F2F2F7; border-radius: 8px; padding: 10px 16px; flex: 1; min-width: 100px; text-align: center; }
        .stat-box .num { font-size: 22px; font-weight: 800; color: #007AFF; }
        .stat-box .lbl { font-size: 10px; color: #8E8E93; margin-top: 2px; font-weight: 600; }

        /* Warning box */
        .warn-box { background: #FFF3E0; border-left: 4px solid #FF9500; padding: 10px 14px; border-radius: 0 8px 8px 0; margin-bottom: 14px; font-size: 11px; color: #7A4100; }
        .danger-box { background: #FFEBEE; border-left: 4px solid #FF3B30; padding: 10px 14px; border-radius: 0 8px 8px 0; margin-bottom: 14px; font-size: 11px; color: #7A0000; }

        /* Tanda tangan */
        .ttd-area { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-box { text-align: center; min-width: 200px; }
        .ttd-box .ttd-line { border-bottom: 1px solid #1C1C1E; margin: 50px 0 6px; }
        .ttd-box p { font-size: 11px; }

        /* Print controls (tidak ikut dicetak) */
        .no-print { background: #1C1C1E; padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 999; }
        .no-print span { color: white; font-size: 14px; font-weight: 600; }
        .btn-print { background: #007AFF; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-back  { background: rgba(255,255,255,0.15); color: white; border: none; padding: 8px 16px; border-radius: 8px; font-size: 13px; cursor: pointer; text-decoration: none; display: inline-block; }

        @media print {
            .no-print { display: none !important; }
            .print-wrap { padding: 10px; max-width: 100%; }
            body { font-size: 11px; }
            thead th { background: #007AFF !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge, .stat-box { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 1cm; size: A4 landscape; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <span>🖨️ {{ $title ?? 'Cetak Laporan' }}</span>
        <div style="display:flex; gap:8px;">
            <a href="{{ url()->previous() }}" class="btn-back">← Kembali</a>
            <button class="btn-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        </div>
    </div>
    <div class="print-wrap">
        @yield('content')
    </div>
</body>
</html>
