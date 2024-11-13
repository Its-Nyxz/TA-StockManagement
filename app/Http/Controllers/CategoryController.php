<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Imports\CategoryImport;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CategoryTemplateExport;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\DeleteCategoryRequest;
use App\Http\Requests\DetailCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.master.barang.jenis');
    }

    public function list(Request $request): JsonResponse
    {
        $category = Category::latest()->get();
        if ($request->ajax()) {
            return DataTables::of($category)
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    if ($data->items()->count() == 0) {
                        $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    }
                    return $button;
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }
    }

    public function save(CreateCategoryRequest $request): JsonResponse
    {

        $category = new Category();
        $category->name = $request->name;
        if ($request->has('description')) {
            $category->description = $request->description;
        }
        $status = $category->save();
        if (!$status) {
            return response()->json(
                ["message" => __("failed to save")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(DetailCategoryRequest $request): JsonResponse
    {
        $id = $request->id;
        $data = Category::find($id);
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(UpdateCategoryRequest $request): JsonResponse
    {
        $id = $request->id;
        $data = Category::find($id);
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

    public function delete(DeleteCategoryRequest $request)
    {
        $id = $request->id;
        $category = Category::find($id);
        $status = $category->delete();
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
            $excelData = Excel::toArray(new CategoryImport, $file);

            // Memeriksa apakah ada data atau tidak
            if (empty($excelData) || empty($excelData[0])) {
                return redirect()->back()->with('error', __('The uploaded file is empty. Please upload a file with data.'));
            }

            Excel::import(new CategoryImport, $request->file('file'));

            return redirect()->back()->with('success', __('Data imported successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed to import data') . '. ' . $e->getMessage());
            // . $e->getMessage()
        }
    }

    public function template()
    {
        return Excel::download(new CategoryTemplateExport, 'jenis_template.xlsx');
    }
}
