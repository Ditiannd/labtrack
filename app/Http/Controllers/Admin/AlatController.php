<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Imports\AlatImport;
use App\Exports\AlatTemplateExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AlatController extends Controller
{
    public function index(Request $request)
    {
        $query = Alat::query()
            ->when($request->q,       fn($q,$s) => $q->where('nama_alat','like',"%$s%")->orWhere('lokasi','like',"%$s%"))
            ->when($request->kategori,fn($q,$k) => $q->where('kategori',$k))
            ->when($request->kondisi, fn($q,$k) => $q->where('kondisi',$k));

        $alat     = $query->latest()->paginate(15)->withQueryString();
        $kategori = Alat::select('kategori')->whereNotNull('kategori')->where('kategori','!=','')->distinct()->orderBy('kategori')->pluck('kategori');
        $grouped  = Alat::whereNotNull('kategori')->where('kategori','!=','')->get()->groupBy('kategori');

        return view('admin.alat.index', compact('alat','kategori','grouped'));
    }

    public function create()
    {
        $kategoriList = Alat::select('kategori')->whereNotNull('kategori')->where('kategori','!=','')->distinct()->orderBy('kategori')->pluck('kategori');
        return view('admin.alat.create', compact('kategoriList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'stok'      => 'required|integer|min:0',
            'kondisi'   => 'required|in:baik,rusak',
            'kategori'  => 'nullable|string|max:100',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama_alat','kategori','stok','kondisi','lokasi','deskripsi']);
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('alat','public');
        }
        Alat::create($data);

        return redirect()->route('admin.alat.index')
            ->with('success', "Alat '{$request->nama_alat}' berhasil ditambahkan.");
    }

    public function edit(Alat $alat)
    {
        $kategoriList = Alat::select('kategori')->whereNotNull('kategori')->where('kategori','!=','')->distinct()->orderBy('kategori')->pluck('kategori');
        return view('admin.alat.edit', compact('alat','kategoriList'));
    }

    public function update(Request $request, Alat $alat)
    {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'stok'      => 'required|integer|min:0',
            'kondisi'   => 'required|in:baik,rusak',
            'kategori'  => 'nullable|string|max:100',
            'lokasi'    => 'nullable|string|max:255',
            'deskripsi' => 'nullable|string',
            'foto'      => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nama_alat','kategori','stok','kondisi','lokasi','deskripsi']);
        if ($request->hasFile('foto')) {
            if ($alat->foto) Storage::disk('public')->delete($alat->foto);
            $data['foto'] = $request->file('foto')->store('alat','public');
        }
        $alat->update($data);

        return redirect()->route('admin.alat.index')
            ->with('success', "Alat '{$alat->nama_alat}' berhasil diperbarui.");
    }

    public function destroy(Alat $alat)
    {
        if ($alat->foto) Storage::disk('public')->delete($alat->foto);
        $alat->delete();
        return redirect()->route('admin.alat.index')
            ->with('success', 'Alat berhasil dihapus.');
    }

    public function importForm()
    {
        return view('admin.alat.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new AlatImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import selesai: {$import->imported} alat berhasil ditambahkan.";
            if ($import->skipped > 0) {
                $msg .= " {$import->skipped} baris dilewati/diupdate.";
            }

            return redirect()->route('admin.alat.index')
                ->with('success', $msg)
                ->with('import_errors', $import->errors);

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function template()
    {
        return AlatTemplateExport::download();
    }
}
