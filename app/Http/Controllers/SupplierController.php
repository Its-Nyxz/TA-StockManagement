<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Imports\SupplierImport;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupplierTemplateExport;

class SupplierController extends Controller
{
    public function index(): View
    {
        return view('admin.master.supplier');
    }


    public function list(Request $request): JsonResponse
    {
        $suppliers = Supplier::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($suppliers)
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    if ($data->item()->count() == 0) {
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
        $suppliers = new Supplier();
        $suppliers->name = $request->name;
        if ($request->has('email')) {
            $suppliers->email = $request->email;
        }
        if ($request->has("website")) {
            $suppliers->website = $request->website;
        }
        $suppliers->phone_number = $request->phone_number;
        $suppliers->address = $request->address;
        $status = $suppliers->save();
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
        $custormer = Supplier::find($id);
        return response()->json(
            ["data" => $custormer]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $suppliers = Supplier::find($id);
        $suppliers->fill($request->all());
        $status = $suppliers->save();
        if (!$status) {
            return response()->json(
                ["message" => __("data failed to change")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("data berhasil diubah")
        ])->setStatusCode(200);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $custormer = Supplier::find($id);
        $status = $custormer->delete();
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
            $excelData = Excel::toArray(new SupplierImport, $file);

            // Memeriksa apakah ada data atau tidak
            if (empty($excelData) || empty($excelData[0])) {
                return redirect()->back()->with('error', __('The uploaded file is empty. Please upload a file with data.'));
            }

            Excel::import(new SupplierImport, $request->file('file'));

            return redirect()->back()->with('success', __('Data imported successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to import data') . '. ' . $e->getMessage());
            // . $e->getMessage()
        }
    }

    public function template()
    {
        return Excel::download(new SupplierTemplateExport, 'supplier_template.xlsx');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => [
                'required',
                'regex:/^[0-9]{1,20}$/', // Hanya angka, maksimal 20 digit
            ],
        ]);

        $supplier = Supplier::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'phone_number' => $validated['phone_number'],
        ]);

        return response()->json([
            'success' => true,
            'data' => $supplier
        ]);
    }
}
