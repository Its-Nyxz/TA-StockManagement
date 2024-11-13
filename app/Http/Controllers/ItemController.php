<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Unit;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\View\View;
use App\Imports\ItemsImport;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Exports\ItemTemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(): View
    {
        $jenisbarang = Category::all();
        $satuan = Unit::all();
        $merk = Brand::all();
        $supplier = Supplier::all();
        return view('admin.master.barang.index', compact('jenisbarang', 'satuan', 'merk', 'supplier'));
    }
    public function list(Request $request): JsonResponse
    {
        $items = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames')->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($items)
                // ->addColumn('img',function($data){
                //     if(empty($data->image)){
                //         return "<img src='".asset('default.png')."' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                //     }
                //     return "<img src='".asset('storage/barang/'.$data->image)."' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                // })
                ->addColumn('img', function ($data) {
                    $imageUrl = empty($data->image)
                        ? asset('default.png')
                        : asset('storage/barang/' . $data->image);

                    return "<img src='" . $imageUrl . "' style='width:100%;max-width:4rem;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                })
                ->addColumn('category_name', function ($data) {
                    return $data->category->name;
                })
                ->addColumn('unit_name', function ($data) {
                    return $data->unit->name;
                })
                ->addColumn('brand_name', function ($data) {
                    return $data->brand->name;
                })
                ->addColumn('supplier_name', function ($data) {
                    return $data->supplier->name;
                })
                ->addColumn("total", function ($data) {
                    $totalQuantityIn = $data->goodsIns->sum('quantity');
                    $totalQuantityOut = $data->goodsOuts->sum('quantity');
                    $totalQuantityRetur = $data->goodsBacks->sum('quantity');
                    $totalQuantitySO = $data->stockOpnames->sum('quantity');
                    $item = Item::with("unit")->find($data->id);
                    $result = ($item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur) + $totalQuantitySO
                        . "/" . $item->unit->name;
                    $result = max(0, $result);
                    if ($result == 0) {
                        return $result;
                    }
                    return $result;
                })
                ->rawColumns(['total'])

                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['img', 'tindakan'])
                ->make(true);
        }
    }

    public function save(Request $request): JsonResponse
    {
        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'price' => 0,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'supplier_id' => $request->supplier_id,
        ];
        if ($request->file('image') != null) {
            $image = $request->file('image');
            $image->storeAs('public/barang/', $image->hashName());
            $img = $image->hashName();
            $data['image'] = $img;
        }
        Item::create($data);
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames')->find($id);
        $data['category_name'] = $data->category->name;
        $data['unit_name'] = $data->unit->name;
        $stok_awal = $data['quantity'];
        $stok_masuk = $data->goodsIns->sum('quantity');
        $stok_keluar = $data->goodsOuts->sum('quantity');
        $stok_retur = $data->goodsBacks->sum('quantity');
        $stok_opname = $data->stockOpnames->sum('quantity');
        $total_stok = ($stok_awal + $stok_masuk - $stok_keluar - $stok_retur) + $stok_opname;
        $data['total_stok'] = $total_stok;
        // $data ['brand_name'] = $data -> brand -> name;
        // $data ['supplier_name'] = $data -> supplier -> name;
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function detailByCode(Request $request): JsonResponse
    {
        $code = $request->code;
        $data = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames')->where("code", $code)->first();
        $data['category_name'] = $data->category->name;
        $data['unit_name'] = $data->unit->name;
        $stok_awal = $data['quantity'];
        $stok_masuk = $data->goodsIns->sum('quantity');
        $stok_keluar = $data->goodsOuts->sum('quantity');
        $stok_retur = $data->goodsBacks->sum('quantity');
        $stok_opname = $data->stockOpnames->sum('quantity');
        $total_stok = ($stok_awal + $stok_masuk - $stok_keluar - $stok_retur) + $stok_opname;
        $data['total_stok'] = $total_stok;
        // $data ['brand_name'] = $data -> brand -> name;
        // $data ['supplier_name'] = $data -> supplier -> name;
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $item = Item::find($id);
        $data = [
            'name' => $request->name,
            'code' => $request->code,
            // 'price'=>$request->price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'unit_id' => $request->unit_id,
            'supplier_id' => $request->supplier_id
        ];
        if ($request->file('image') != null) {
            Storage::delete('public/barang/' . $item->image);
            $image = $request->file('image');
            $image->storeAs('public/barang/', $image->hashName());
            $img = $image->hashName();
            $data['image'] = $img;
        }
        $item->fill($data);
        $item->save();
        return response()->json([
            "message" => __("data changed successfully")
        ])->setStatusCode(200);
    }

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $item = Item::find($id);
        Storage::delete('public/barang/' . $item->image);
        $status = $item->delete();
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
        // Validate that a file is uploaded and it is in the correct format
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            // Load data from the file to check if it contains any rows
            $file = $request->file('file');
            $excelData = Excel::toArray(new ItemsImport, $file);

            // Check if the file is empty
            if (empty($excelData) || empty($excelData[0])) {
                return redirect()->back()->with('error', __('The uploaded file is empty. Please upload a file with data.'));
            }

            // Proceed with import if file has data
            Excel::import(new ItemsImport, $file);

            return redirect()->back()->with('success', 'Data imported successfully');
        } catch (\Exception $e) {
            // Log and return error if import fails
            return redirect()->back()->with('error', 'Failed to import data. ' . $e->getMessage());
        }
    }

    public function template()
    {
        return Excel::download(new ItemTemplateExport, 'barang_template.xlsx');
    }
}
