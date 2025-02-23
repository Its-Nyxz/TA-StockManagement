<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\Brand;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\Supplier;
use App\Models\GoodsBack;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Can;
use App\Http\Requests\StoreStockOpnameRequest;
use App\Http\Requests\UpdateStockOpnameRequest;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $in_status = Item::where('active', 'true')->count();
        $suppliers = Supplier::all();
        $brands = Brand::all();
        $users = User::all();
        return view('admin.master.laporan.so', compact('in_status', 'suppliers', 'brands', 'users'));
    }

    public function list(Request $request): JsonResponse
    {
        $so = StockOpname::with('item', 'user', 'supplier');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $so->whereBetween('date_so', [$request->start_date, $request->end_date]);
        }

        if (!empty($request->inputer)) {
            $so->where('user_id', $request->inputer);
        }

        if (!empty($request->supplier_id)) {
            $so->where('supplier_id', [$request->supplier_id]);
        }

        // Filter berdasarkan brand
        if (!empty($request->brands)) {
            $so->whereHas('item.brand', function ($query) use ($request) {
                $query->where('id', $request->brands);
            });
        }

        // Filter berdasarkan item_name
        if (!empty($request->item_name)) {
            $so->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->item_name . '%');
            });
        }

        if (Auth::user()->role->id > 2) {
            $so->where('user_id', Auth::user()->id);
        };
        $so->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($so)
                ->addColumn('quantity', function ($data) {
                    // Pastikan relasi unit sudah dimuat sebelumnya
                    $unitName = $data->item->unit->name ?? '';
                    return number_format($data->quantity, 2) . ' / ' . $unitName;
                })

                ->addColumn('stok_sistem', function ($data) {
                    $item = Item::with("unit")->find($data->item->id);
                    return $data->stok_sistem . ' / ' . $item->unit->name;
                })
                ->addColumn('stok_fisik', function ($data) {
                    $item = Item::with("unit")->find($data->item->id);
                    return $data->stok_fisik . ' / ' . $item->unit->name;
                })
                ->addColumn("date_so", function ($data) {
                    return Carbon::parse($data->date_so)->format('d F Y');
                })
                ->addColumn("kode_barang", function ($data) {
                    return $data->item->code;
                })
                ->addColumn("supplier_name", function ($data) {
                    return $data->supplier->name;
                })
                ->addColumn("item_name", function ($data) {
                    return $data->item->name;
                })

                ->addColumn("status", function ($data) {
                    $stokSistem = $data->stok_sistem;
                    $stokFisik = $data->stok_fisik;

                    if ($stokSistem < $stokFisik) {
                        return "<span class='badge badge-success'>" . __("Stock Increases") . "</span>";
                    } elseif ($stokSistem > $stokFisik) {
                        return "<span class='badge badge-danger'>" . __("Stock Decreases") . "</span>";
                    } else {
                        return "<span class='badge badge-primary'>" . __("Stock is Correct") . "</span>";
                    }
                })

                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['tindakan', 'status'])
                ->make(true);
        }
    }

    public function save(Request $request): JsonResponse
    {
        $requestedQuantityInBaseUnit = $request->quantity / $request->conversion_factor;
        $requestedStockSistemInBaseUnit = $request->stock_sistem / $request->conversion_factor;
        $requestedStockFisikInBaseUnit = $request->stock_fisik / $request->conversion_factor;

        $data = [
            'user_id' => $request->user_id,
            'date_so' => $request->date_so,
            // 'quantity' => $request->quantity,
            'quantity' => $requestedQuantityInBaseUnit, // Jumlah dalam satuan input
            'description' => $request->description,
            'supplier_id' => $request->supplier_id,
            'invoice_number' => $request->invoice_number,
            // 'stok_sistem' => $request->stock_sistem,
            // 'stok_fisik' => $request->stock_fisik,
            'stok_sistem' => $requestedStockSistemInBaseUnit,
            'stok_fisik' => $requestedStockFisikInBaseUnit,
            'item_id' => $request->item_id
        ];
        StockOpname::create($data);
        $barang = Item::find($request->item_id);
        $barang->active = "true";
        $barang->save();
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = StockOpname::with('User')->where('id', $id)->first();
        $barang = Item::with('category', 'unit', 'conversions.fromUnit', 'conversions.toUnit')->find($data->item_id);
        $data['kode_barang'] = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang'] = $barang->category->name;
        $data['nama_barang'] = $barang->name;
        $data['user_id'] = $data->user_id;
        $data['description'] = $data->description;
        $data['supplier_id'] = $data->supplier_id;
        $data['id_barang'] = $barang->id;
        $stok_sistem = $data->stok_sistem;
        $stok_fisik = $data->stok_fisik;
        if ($stok_sistem < $stok_fisik) {
            $status = __("Stock Increases");
        } elseif ($stok_sistem > $stok_fisik) {
            $status = __("Stock Decreases");
        } else {
            $status = __("Stock is Correct");
        }
        $data['status'] = $status;
        $data['conversions'] = $barang->conversions->map(function ($conv) {
            return [
                'from_unit_id' => $conv->from_unit_id,
                'to_unit_id' => $conv->to_unit_id,
                'conversion_factor' => $conv->conversion_factor,
                'from_unit_name' => optional($conv->fromUnit)->name ?? 'N/A',
                'to_unit_name' => optional($conv->toUnit)->name ?? 'N/A',
            ];
        });
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = StockOpname::find($id);
        $requestedQuantityInBaseUnit = $request->quantity / $request->conversionFactor;
        $requestedStockSistemInBaseUnit = $request->stock_sistem / $request->conversion_factor;
        $requestedStockFisikInBaseUnit = $request->stock_fisik / $request->conversion_factor;
        $data->user_id = $request->user_id;
        $data->date_so = $request->date_so;
        // $data->quantity = $request->quantity;
        $data->quantity = $requestedQuantityInBaseUnit; // Simpan dalam base unit
        // $data->stok_sistem = $request->stock_sistem;
        // $data->stok_fisik = $request->stock_fisik;
        $data->stok_sistem = $requestedStockSistemInBaseUnit;
        $data->stok_fisik = $requestedStockFisikInBaseUnit;
        $data->item_id = $request->item_id;
        $data->description = $request->description;
        $data->supplier_id = $request->supplier_id;
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

    public function delete(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = StockOpname::find($id);
        $status = $data->delete();
        if (!$status) {
            return response()->json(
                ["message" => __("data failed to delete")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message" => __("data deleted successfully")
        ])->setStatusCode(200);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockOpnameRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(StockOpname $stockOpname)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StockOpname $stockOpname)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateStockOpnameRequest $request, StockOpname $stockOpname)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockOpname $stockOpname)
    {
        //
    }
}
