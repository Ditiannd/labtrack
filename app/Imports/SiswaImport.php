<?php
namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SiswaImport implements ToCollection, SkipsEmptyRows
{
    public int   $imported = 0;
    public int   $skipped  = 0;
    public array $errors   = [];

    private array $colMap       = [];
    private bool  $headerParsed = false;

    private function parseHeader(Collection $row): void
    {
        foreach ($row->values() as $i => $val) {
            if ($val === null || $val === '') continue;
            $key = strtolower(trim((string) $val));
            $key = preg_replace('/[\s\-_]+/', '_', $key);
            $key = preg_replace('/[^a-z0-9_]/', '', $key);
            $this->colMap[$key] = $i;
        }
        $this->headerParsed = true;
    }

    private function col(Collection $row, string $key): string
    {
        if (isset($this->colMap[$key])) {
            $val = $row->values()->get($this->colMap[$key]);
            return trim((string) ($val ?? ''));
        }
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

            // Skip baris kosong
            $allEmpty = $row->every(fn($v) => $v === null || trim((string)$v) === '');
            if ($allEmpty) { $this->skipped++; continue; }

            $nis  = $this->col($row, 'nis');
            $nama = $this->col($row, 'nama');

            // Jika NIS berupa angka desimal dari Excel (misal 2024001.0)
            if ($nis && is_numeric($nis)) {
                $nis = (string)(int)(float)$nis;
            }

            if (!$nis || !$nama) {
                $this->errors[] = "Baris ".($rowIndex+1).": NIS/Nama kosong, dilewati.";
                $this->skipped++;
                continue;
            }

            // Skip header duplikat
            if (in_array(strtolower($nis), ['nis']) || in_array(strtolower($nama), ['nama'])) {
                $this->skipped++;
                continue;
            }

            // NIS sudah ada
            if (Siswa::where('nis', $nis)->exists()) {
                $this->errors[] = "Baris ".($rowIndex+1).": NIS '{$nis}' sudah ada, dilewati.";
                $this->skipped++;
                continue;
            }

            // Susun email unik
            $email = $this->col($row, 'email');
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $slug  = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $nama));
                $email = "{$slug}_{$nis}@siswa.lab";
            }
            // Jaga keunikan email
            $baseEmail = $email;
            $counter   = 1;
            while (User::where('email', $email)->exists()) {
                $email = str_replace('@', "{$counter}@", $baseEmail);
                $counter++;
            }

            $password = $this->col($row, 'password') ?: $nis;
            $kelas    = $this->col($row, 'kelas');
            $jurusan  = $this->col($row, 'jurusan');
            $angkatan = $this->col($row, 'angkatan');
            // Angkatan bisa berupa angka desimal dari Excel
            if ($angkatan && is_numeric($angkatan)) {
                $angkatan = (string)(int)(float)$angkatan;
            }

            try {
                DB::transaction(function () use ($nis, $nama, $email, $password, $kelas, $jurusan, $angkatan) {
                    $user = User::create([
                        'name'     => $nama,
                        'email'    => $email,
                        'password' => Hash::make($password),
                        'role'     => 'siswa',
                    ]);
                    Siswa::create([
                        'nama'     => $nama,
                        'nis'      => $nis,
                        'kelas'    => $kelas,
                        'jurusan'  => $jurusan,
                        'angkatan' => $angkatan,
                        'id_user'  => $user->id,
                    ]);
                });
                $this->imported++;
            } catch (\Throwable $e) {
                $this->errors[] = "Baris ".($rowIndex+1).": Gagal — ".$e->getMessage();
                $this->skipped++;
            }
        }
    }
}
