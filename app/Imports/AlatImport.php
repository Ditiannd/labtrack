<?php
namespace App\Imports;

use App\Models\Alat;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AlatImport implements ToCollection, SkipsEmptyRows
{
    public int   $imported = 0;
    public int   $skipped  = 0;
    public array $errors   = [];

    // Map: nama kolom (lowercase, trim) => index kolom
    private array $colMap = [];
    private bool  $headerParsed = false;

    /**
     * Temukan index kolom dari baris header secara manual
     * Ini cara paling robust — tidak bergantung formatter maatwebsite
     */
    private function parseHeader(Collection $row): void
    {
        foreach ($row->values() as $i => $val) {
            if ($val === null || $val === '') continue;
            // Normalisasi: lowercase, hapus spasi/strip/underscore berlebih
            $key = strtolower(trim((string) $val));
            $key = preg_replace('/[\s\-_]+/', '_', $key);
            $key = preg_replace('/[^a-z0-9_]/', '', $key);
            $this->colMap[$key] = $i;
        }
        $this->headerParsed = true;
    }

    private function col(Collection $row, string $key): string
    {
        // Coba exact match dulu
        if (isset($this->colMap[$key])) {
            $val = $row->values()->get($this->colMap[$key]);
            return trim((string) ($val ?? ''));
        }
        // Coba partial match (tanpa underscore)
        $target = str_replace('_', '', $key);
        foreach ($this->colMap as $k => $i) {
            if (str_replace('_', '', $k) === $target) {
                $val = $row->values()->get($i);
                return trim((string) ($val ?? ''));
            }
        }
        return '';
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            // Baris pertama = header
            if (!$this->headerParsed) {
                $this->parseHeader($row);
                continue;
            }

            // Skip baris kosong total
            $allEmpty = $row->every(fn($v) => $v === null || trim((string)$v) === '');
            if ($allEmpty) { $this->skipped++; continue; }

            $namaAlat = $this->col($row, 'nama_alat');

            // Fallback: jika nama_alat tidak ketemu coba kolom pertama yang tidak kosong
            if (!$namaAlat) {
                $namaAlat = trim((string) $row->values()->first(fn($v) => $v !== null && trim((string)$v) !== ''));
            }

            if (!$namaAlat) {
                $this->skipped++;
                continue;
            }

            // Skip jika isinya mirip header
            if (in_array(strtolower($namaAlat), ['nama_alat','nama alat','nama','alat'])) {
                $this->skipped++;
                continue;
            }

            $kondisi = strtolower($this->col($row, 'kondisi') ?: 'baik');
            if (!in_array($kondisi, ['baik','rusak'])) $kondisi = 'baik';

            $stokRaw = $this->col($row, 'stok');
            $stok    = is_numeric($stokRaw) ? (int)$stokRaw : 0;
            if ($stok < 0) $stok = 0;

            $kategori  = $this->col($row, 'kategori');
            $lokasi    = $this->col($row, 'lokasi');
            $deskripsi = $this->col($row, 'deskripsi');

            // Nama sama + lokasi sama → tambah stok
            $existing = Alat::where('nama_alat', $namaAlat)
                ->where('lokasi', $lokasi)
                ->first();

            if ($existing) {
                $existing->update([
                    'stok'      => $existing->stok + $stok,
                    'kondisi'   => $kondisi,
                    'kategori'  => $kategori  ?: $existing->kategori,
                    'deskripsi' => $deskripsi ?: $existing->deskripsi,
                ]);
                $this->errors[] = "Baris ".($rowIndex+1).": '{$namaAlat}' sudah ada → stok ditambah {$stok}.";
                $this->skipped++;
            } else {
                Alat::create([
                    'nama_alat' => $namaAlat,
                    'kategori'  => $kategori,
                    'stok'      => $stok,
                    'kondisi'   => $kondisi,
                    'lokasi'    => $lokasi,
                    'deskripsi' => $deskripsi,
                ]);
                $this->imported++;
            }
        }
    }
}
