<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\View\View;
use App\Imports\UnitImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Exports\UnitTemplateExport;
use Maatwebsite\Excel\Facades\Excel;

class UnitController extends Controller
{
    public function index(): View
    {
        return view('admin.master.barang.satuan');
    }

    public function list(Request $request): JsonResponse
    {
        $units = Unit::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($units)
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    if (
                        $data->items()->count() == 0  && $data->fromConversions()->count() == 0 &&
                        $data->toConversions()->count() == 0
                    ) {
                        $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    }
                    return $button;
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }
    }

    public function save(Request $request): JsonResponse
    {

        $units = new Unit();
        $units->name = $request->name;
        if ($request->has('description')) {
            $units->description = $request->description;
        }
        $status = $units->save();
        if (!$status) {
            return response()->json(
                ["message" => __("failed to save")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Unit::find($id);
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Unit::find($id);
        $data->fill($request->all());
        $status = $data->save();
        if (!$status) {
            return response()->json(
                ["message" => __("data failed to change")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("data changed successfully")
        ])->setStatusCode(200);
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $units = Unit::find($id);
        $status = $units->delete();
        if (!$status) {
            return response()->json(
                ["message" => __("data failed to delete")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("data deleted successfully")
        ])->setStatusCode(200);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            // Memuat data dari file untuk memeriksa apakah kosong
            $file = $request->file('file');
            $excelData = Excel::toArray(new UnitImport, $file);

            // Memeriksa apakah ada data atau tidak
            if (empty($excelData) || empty($excelData[0])) {
                return redirect()->back()->with('error', __('The uploaded file is empty. Please upload a file with data.'));
            }

            Excel::import(new UnitImport, $request->file('file'));

            return redirect()->back()->with('success', __('Data imported successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to import data') . '. ' . $e->getMessage());
            // . $e->getMessage()
        }
    }

    public function template()
    {
        return Excel::download(new UnitTemplateExport, 'satuan_template.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $slug = Str::slug($validated['name']);

        // Cek apakah slug sudah ada di database
        if (Unit::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan dengan nama ini sudah terdaftar.'
            ], 422); // 422: Unprocessable Entity
        }

        // Jika tidak ada, buat data baru
        $satuan = Unit::create([
            'name' => $validated['name'],
            'slug' => $slug
        ]);

        return response()->json([
            'success' => true,
            'data' => $satuan
        ]);
    }
}
