<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use App\Imports\SiswaImport;
use App\Exports\SiswaTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::with('user')
            ->when($request->q,       fn($q,$s) => $q->where('nama','like',"%$s%")->orWhere('nis','like',"%$s%"))
            ->when($request->jurusan, fn($q,$j) => $q->where('jurusan',$j))
            ->when($request->kelas,   fn($q,$k) => $q->where('kelas',$k));

        $siswa   = $query->latest()->paginate(20)->withQueryString();
        $jurusan = Siswa::select('jurusan')->whereNotNull('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        $kelas   = Siswa::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $grouped = Siswa::with('user')->whereNotNull('jurusan')->get()->groupBy('jurusan');

        return view('admin.siswa.index', compact('siswa','jurusan','kelas','grouped'));
    }

    public function create()
    {
        $jurusanList = Siswa::select('jurusan')->whereNotNull('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        return view('admin.siswa.create', compact('jurusanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'nis'      => 'required|string|unique:siswa,nis',
            'kelas'    => 'required|string|max:50',
            'jurusan'  => 'nullable|string|max:100',
            'angkatan' => 'nullable|string|max:10',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'siswa',
            ]);
            Siswa::create([
                'nama'     => $request->nama,
                'nis'      => $request->nis,
                'kelas'    => $request->kelas,
                'jurusan'  => $request->jurusan,
                'angkatan' => $request->angkatan,
                'id_user'  => $user->id,
            ]);
        });

        return redirect()->route('admin.siswa.index')
            ->with('success', "Siswa '{$request->nama}' berhasil ditambahkan.");
    }

    public function edit(Siswa $siswa)
    {
        $jurusanList = Siswa::select('jurusan')->whereNotNull('jurusan')->distinct()->orderBy('jurusan')->pluck('jurusan');
        return view('admin.siswa.edit', compact('siswa','jurusanList'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'nis'      => 'required|string|unique:siswa,nis,'.$siswa->id_siswa.',id_siswa',
            'kelas'    => 'required|string|max:50',
            'jurusan'  => 'nullable|string|max:100',
            'angkatan' => 'nullable|string|max:10',
        ]);

        $siswa->update($request->only(['nama','nis','kelas','jurusan','angkatan']));
        if ($siswa->user) $siswa->user->update(['name' => $request->nama]);

        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        DB::transaction(function () use ($siswa) {
            $user = $siswa->user;
            $siswa->delete();
            if ($user) $user->delete();
        });
        return redirect()->route('admin.siswa.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    public function importForm()
    {
        return view('admin.siswa.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new SiswaImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import selesai: {$import->imported} siswa berhasil ditambahkan.";
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} baris dilewati.";
            }

            return redirect()->route('admin.siswa.index')
                ->with('success', $msg)
                ->with('import_errors', $import->errors);

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        return SiswaTemplateExport::download();
    }
}
