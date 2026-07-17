<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;

class RawImport implements ToCollection, WithHeadingRow
{
    public $rows = [];
    public function collection(Collection $rows) { $this->rows = $rows->toArray(); }
}

class DebugImportController extends Controller
{
    public function debug(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'No file']);
        }
        $import = new RawImport();
        Excel::import($import, $request->file('file'));
        return response()->json([
            'total_rows' => count($import->rows),
            'first_row'  => $import->rows[0] ?? null,
            'all_keys'   => isset($import->rows[0]) ? array_keys($import->rows[0]) : [],
        ]);
    }
}
