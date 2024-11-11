<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\View\View;
use App\Imports\BrandImport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Exports\BrandTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\CreateBrandRequest;
use App\Http\Requests\DeleteBrandRequest;
use App\Http\Requests\DetailBrandRequest;
use App\Http\Requests\UpdateBrandRequest;

// Represetation Class Controller Brand

class BrandController extends Controller
{
    // return view page barand
    public function index(): View
    {
        return view('admin.master.barang.merk');
    }

    // return list brand in format json
    public function list(Request $request): JsonResponse
    {
        $brands = Brand::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($brands)
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }
    }

    // save new brand
    public function save(CreateBrandRequest $request): JsonResponse
    {

        $brands = new Brand();
        $brands->name = $request->name;
        if ($request->has('description')) {
            $brands->description = $request->description;
        }
        $status = $brands->save();
        if (!$status) {
            return response()->json(
                ["message" => __("failed to save")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    // get detail brand
    public function detail(DetailBrandRequest $request): JsonResponse
    {
        $id = $request->id;
        $data = Brand::find($id);
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    // update brand
    public function update(UpdateBrandRequest $request): JsonResponse
    {
        $id = $request->id;
        $data = Brand::find($id);
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

    // delete brand
    public function delete(DeleteBrandRequest $request)
    {
        $id = $request->id;
        $brands = Brand::find($id);
        $status = $brands->delete();
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
            $excelData = Excel::toArray(new BrandImport, $file);

            // Memeriksa apakah ada data atau tidak
            if (empty($excelData) || empty($excelData[0])) {
                return redirect()->back()->with('error', __('The uploaded file is empty. Please upload a file with data.'));
            }

            Excel::import(new BrandImport, $request->file('file'));

            return redirect()->back()->with('success', __('Data imported successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to import data') . '. ' . $e->getMessage());
            // . $e->getMessage()
        }
    }

    public function template()
    {
        return Excel::download(new BrandTemplateExport, 'brand_template.xlsx');
    }
}
