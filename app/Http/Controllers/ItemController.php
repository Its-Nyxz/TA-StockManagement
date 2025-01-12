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
use App\Models\UnitConversion;
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
        $items = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames', 'conversions.fromUnit', 'conversions.toUnit')->latest()->get();
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

                    // Mengambil item dengan relasi unit
                    $item = Item::with("unit")->find($data->id);
                    if (!$item || !$item->unit) {
                        return '0.00'; // Default jika item atau unit tidak ditemukan
                    }

                    // Hitung total stok
                    $totalStock = ($item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur) + $totalQuantitySO;
                    $totalStock = max(0, $totalStock); // Pastikan tidak negatif

                    // Format angka dan tambahkan unit
                    $formattedTotal = number_format($totalStock, 2); // Format dengan 2 desimal
                    return $formattedTotal . " / " . $item->unit->name;
                })
                ->rawColumns(['total'])
                ->addColumn('conversions', function ($data) {
                    if ($data->conversions->isEmpty()) {
                        return '-'; // This will ensure no content is shown for empty conversions.
                    }

                    return $data->conversions->map(function ($conv) {
                        $fromUnit = optional($conv->fromUnit)->name ?? 'N/A';
                        $toUnit = optional($conv->toUnit)->name ?? 'N/A';
                        return "{$fromUnit} → {$toUnit}: {$conv->conversion_factor}";
                    })->join(' || ');
                })
                ->rawColumns(['conversions']) // Tambahkan ini agar HTML dirender
                ->addColumn('tindakan', function ($data) {
                    // Menggunakan count untuk memeriksa apakah item digunakan di tabel lain
                    $goodsInsCount = $data->goodsIns->count();
                    $goodsOutsCount = $data->goodsOuts->count();
                    $goodsBacksCount = $data->goodsBacks->count();
                    $stockOpnamesCount = $data->stockOpnames->count();

                    // Jika ada entri di tabel lain, tombol hapus tidak akan ditampilkan
                    if ($goodsInsCount > 0 || $goodsOutsCount > 0 || $goodsBacksCount > 0 || $stockOpnamesCount > 0) {
                        $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                        return $button; // Hanya tombol Edit jika item sudah digunakan
                    }

                    // Jika item tidak digunakan di tabel lain, tampilkan tombol hapus
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['img', 'tindakan'])
                ->make(mDataSupport: true);
        }
    }

    public function save(Request $request): JsonResponse
    {
        // dd($request->all()); // Debug: hentikan proses dan tampilkan data
        try {
            // Data barang
            $data = [
                'name' => $request->name,
                'code' => $request->code,
                'stock_limit' => $request->stock_limit,
                'price' => 0,
                'category_id' => $request->category_id,
                'brand_id' => $request->brand_id,
                'unit_id' => $request->unit_id,
                'supplier_id' => $request->supplier_id,
            ];

            // Upload gambar jika ada
            if ($request->hasFile('image')) {
                $data['image'] = $this->uploadImage($request->file('image'));
            }

            // Simpan barang
            $item = Item::create($data);
            // Simpan data konversi satuan
            if ($request->has('conversions')) {
                foreach ($request->conversions as $conversion) {
                    UnitConversion::create([
                        'item_id' => $item->id,
                        'from_unit_id' => $conversion['from_unit_id'],
                        'to_unit_id' => $conversion['to_unit_id'],
                        'conversion_factor' => $conversion['conversion_factor'],
                    ]);
                }
            }

            return response()->json([
                'message' => __('Berhasil Menyimpan Data')
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('Gagal Menyimpan Data'),
                'error' => $e->getMessage(),
            ])->setStatusCode(500);
        }
    }

    /**
     * Upload image to storage
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @return string $imageName
     */
    private function uploadImage($image): string
    {
        $imageName = $image->hashName();
        $image->storeAs('public/barang/', $imageName);
        return $imageName;
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames', 'conversions.fromUnit', 'conversions.toUnit')->find($id);
        $data['category_name'] = $data->category->name;
        $data['unit_name'] = $data->unit->name;
        $stok_awal = $data['quantity'];
        $stok_masuk = $data->goodsIns->sum('quantity');
        $stok_keluar = $data->goodsOuts->sum('quantity');
        $stok_retur = $data->goodsBacks->sum('quantity');
        $stok_opname = $data->stockOpnames->sum('quantity');
        $total_stok = ($stok_awal + $stok_masuk - $stok_keluar - $stok_retur) + $stok_opname;
        $data['total_stok'] = $total_stok;

        // Tambahkan conversions dalam format array
        $data['conversions'] = $data->conversions->map(function ($conv) {
            return [
                'id' => $conv->id, // Tambahkan ID konversi
                'from_unit_id' => $conv->from_unit_id,
                'to_unit_id' => $conv->to_unit_id,
                'conversion_factor' => $conv->conversion_factor,
                'from_unit_name' => optional($conv->fromUnit)->name ?? 'N/A',
                'to_unit_name' => optional($conv->toUnit)->name ?? 'N/A',
            ];
        });
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
            'supplier_id' => $request->supplier_id,
            'stock_limit' => $request->stock_limit,
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
        // dd($item);

        if ($request->has('from_unit') && $request->has('to_unit') && $request->has('conversion_factor')) {
            if (empty($request->from_unit) || empty($request->to_unit) || empty($request->conversion_factor)) {
                // Jika data konversi kosong, hapus semua konversi terkait
                $item->conversions()->delete();
            } else {
                // Jika ada ID konversi, hapus yang tidak termasuk dalam daftar
                if ($request->filled('conversion_ids')) {
                    $item->conversions()->whereNotIn('id', $request->conversion_ids)->delete();
                } else {
                    // Jika tidak ada ID sama sekali, hapus semua konversi terkait item
                    $item->conversions()->delete();
                }

                // Perbarui atau buat konversi baru
                foreach ($request->from_unit as $index => $fromUnit) {
                    UnitConversion::updateOrCreate(
                        [
                            'id' => $request->conversion_ids[$index] ?? null // Jika ID ada, update, jika tidak, buat baru
                        ],
                        [
                            'item_id' => $item->id,
                            'from_unit_id' => $fromUnit,
                            'to_unit_id' => $request->to_unit[$index],
                            'conversion_factor' => $request->conversion_factor[$index],
                        ]
                    );
                }
            }
        } else {
            // Jika tidak ada data konversi sama sekali dalam request, hapus semua konversi terkait
            $item->conversions()->delete();
        }


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
            return redirect()->back()->with('error', 'Gagal Mengimport Data. ' . $e->getMessage());
        }
    }

    public function template()
    {
        return Excel::download(new ItemTemplateExport, 'barang_template.xlsx');
    }
}
