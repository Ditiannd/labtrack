<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alat;

class DebugController extends Controller
{
    /**
     * GET /api/debug/preview  (local only)
     *
     * Mengembalikan HTML biasa (bukan JSON) sebagai demonstrasi bahwa
     * tab "Preview" di panel response Bruno bisa merender HTML, bukan
     * cuma menampilkan JSON mentah. Route ini hanya untuk keperluan
     * demo/dokumentasi, tidak dipakai oleh aplikasi mobile/SPA.
     */
    public function preview()
    {
        $alat = $this->sampleAlat();

        $rows = $alat->map(function ($item) {
            return '<tr>'
                .'<td>'.e($item['id_alat']).'</td>'
                .'<td>'.e($item['nama_alat']).'</td>'
                .'<td>'.e($item['kategori']).'</td>'
                .'<td>'.e($item['stok']).'</td>'
                .'<td>'.e($item['kondisi']).'</td>'
                .'<td>'.e($item['lokasi']).'</td>'
                .'</tr>';
        })->implode('');

        $html = <<<HTML
        <!doctype html>
        <html lang="id">
        <head>
            <meta charset="utf-8">
            <title>LabTrack — Debug Preview</title>
            <style>
                body { font-family: -apple-system, Segoe UI, Roboto, sans-serif; margin: 2rem; color: #1f2937; }
                h1 { font-size: 1.25rem; margin-bottom: .25rem; }
                p.sub { color: #6b7280; margin-top: 0; }
                table { border-collapse: collapse; width: 100%; margin-top: 1rem; }
                th, td { border: 1px solid #e5e7eb; padding: .5rem .75rem; text-align: left; font-size: .9rem; }
                th { background: #f3f4f6; }
                tr:nth-child(even) { background: #fafafa; }
                .badge { display: inline-block; padding: .1rem .5rem; border-radius: 999px; background: #dbeafe; color: #1e40af; font-size: .75rem; }
            </style>
        </head>
        <body>
            <h1>LabTrack &mdash; Contoh Response HTML</h1>
            <p class="sub">
                Endpoint ini sengaja mengembalikan <span class="badge">text/html</span>,
                bukan JSON, untuk mendemonstrasikan tab <strong>Preview</strong> di Bruno.
            </p>
            <table>
                <thead>
                    <tr><th>ID</th><th>Nama Alat</th><th>Kategori</th><th>Stok</th><th>Kondisi</th><th>Lokasi</th></tr>
                </thead>
                <tbody>
                    {$rows}
                </tbody>
            </table>
        </body>
        </html>
        HTML;

        return response($html)->header('Content-Type', 'text/html');
    }

    /**
     * GET /api/debug/dd-example  (local only)
     *
     * Memanggil dd() Laravel langsung terhadap sebagian data (daftar alat).
     * Laravel dd() mengembalikan halaman HTML bergaya (Symfony VarDumper),
     * sehingga tab Preview di Bruno akan merendernya sebagai halaman,
     * bukan sekadar teks mentah.
     */
    public function ddExample()
    {
        $data = [
            'catatan' => 'Ini contoh dd() bawaan Laravel — hanya aktif di environment local.',
            'alat' => $this->sampleAlat()->toArray(),
        ];

        dd($data);
    }

    /**
     * Data contoh alat untuk kebutuhan demo, tidak menyentuh tabel asli
     * agar endpoint ini aman dipanggil kapan pun tanpa efek samping.
     */
    private function sampleAlat()
    {
        $alat = Alat::query()->limit(5)->get(['id_alat', 'nama_alat', 'kategori', 'stok', 'kondisi', 'lokasi']);

        if ($alat->isEmpty()) {
            $alat = collect([
                ['id_alat' => 1, 'nama_alat' => 'Mikroskop Binokuler', 'kategori' => 'Biologi', 'stok' => 8, 'kondisi' => 'baik', 'lokasi' => 'Lab Biologi - Rak A'],
                ['id_alat' => 2, 'nama_alat' => 'Multimeter Digital', 'kategori' => 'Elektronika', 'stok' => 12, 'kondisi' => 'baik', 'lokasi' => 'Lab Elektronika - Rak B'],
                ['id_alat' => 3, 'nama_alat' => 'Gelas Ukur 500ml', 'kategori' => 'Kimia', 'stok' => 20, 'kondisi' => 'rusak', 'lokasi' => 'Lab Kimia - Rak C'],
            ])->map(fn ($row) => (object) $row);
        }

        return $alat->map(fn ($item) => [
            'id_alat' => $item->id_alat,
            'nama_alat' => $item->nama_alat,
            'kategori' => $item->kategori,
            'stok' => $item->stok,
            'kondisi' => $item->kondisi,
            'lokasi' => $item->lokasi,
        ]);
    }
}
