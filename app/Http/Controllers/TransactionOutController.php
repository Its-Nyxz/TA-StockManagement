<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\User;
use App\Models\GoodsIn;
use App\Models\Customer;
use App\Models\GoodsOut;
use App\Models\Supplier;
use App\Models\GoodsBack;
use Illuminate\View\View;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use App\Models\UnitConversion;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionOutController extends Controller
{
    public function index(): View
    {
        $in_status = Item::where('active', 'true')->count();
        $customers = Customer::all();
        $users = User::all();
        $suppliers = Supplier::all();
        $brands = Brand::all();
        return view('admin.master.transaksi.keluar', compact('customers', 'in_status', 'users', 'suppliers', 'brands'));
    }

    public function list(Request $request): JsonResponse
    {
        // Query dasar dengan relasi yang diperlukan
        $goodsouts = GoodsOut::with('item.brand', 'user', 'supplier');

        // Filter berdasarkan rentang tanggal
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsouts->whereBetween('date_out', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan user inputer
        if (!empty($request->inputer)) {
            $goodsouts->where('user_id', $request->inputer);
        }

        // Filter berdasarkan supplier
        if (!empty($request->suppliers)) {
            $goodsouts->where('supplier_id', $request->suppliers);
        }

        // Filter berdasarkan brand
        if (!empty($request->brands)) {
            $goodsouts->whereHas('item.brand', function ($query) use ($request) {
                $query->where('id', $request->brands);
            });
        }

        // Filter berdasarkan item_name
        if (!empty($request->item_name)) {
            $goodsouts->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->item_name . '%');
            });
        }

        // Batasi data untuk pengguna dengan peran tertentu
        if (Auth::user()->role->id > 2) {
            $goodsouts->where('user_id', Auth::user()->id);
        }
        $goodsouts->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($goodsouts)
                ->addColumn('quantity', function ($data) {
                    // Pastikan relasi unit sudah dimuat sebelumnya
                    $unitName = $data->item->unit->name ?? '';
                    return number_format($data->quantity, 2) . " / " . $unitName;
                })
                ->addColumn("date_out", function ($data) {
                    return Carbon::parse($data->date_out)->format('d F Y');
                })
                ->addColumn("kode_barang", function ($data) {
                    return $data->item->code;
                })
                // ->addColumn("costumer_name",function($data){
                //     return $data -> costumer -> name;
                // })
                ->addColumn("pemasok", function ($data) {
                    return $data->supplier->name;
                })
                ->addColumn("brand", function ($data) {
                    return $data->item->brand->name;
                })

                ->addColumn("item_name", function ($data) {
                    return $data->item->name;
                })
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("Edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("Delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['tindakan'])
                ->make(true);
        }
    }

    public function save(Request $request): JsonResponse
    {

        // Konversi jumlah input ke base unit (sak)
        $requestedQuantityInBaseUnit = $request->quantity / $request->conversion_factor;
        // dd($requestedQuantityInBaseUnit);
        // Hitung total stok dalam base unit (sak)
        $totalStockInBaseUnit = (
            (Item::where('id', $request->item_id)->sum('quantity') +
                GoodsIn::where('item_id', $request->item_id)->sum('quantity') -
                GoodsOut::where('item_id', $request->item_id)->sum('quantity') -
                GoodsBack::where('item_id', $request->item_id)->sum('quantity')) +
            StockOpname::where('item_id', $request->item_id)->sum('quantity')
        );
        // dd($totalStockInBaseUnit);
        // Validasi stok
        if ($requestedQuantityInBaseUnit > $totalStockInBaseUnit) {
            return response()->json([
                "message" => __("Stok tidak mencukupi")
            ])->setStatusCode(400);
        }
        // $totalStock = max(0, $item + $goodsIn - $goodsOut - $goodsBack + $stockOpname);

        // if ($request->quantity > $totalStock || $totalStock === 0) {
        //     return  response()->json([
        //         "message" => __("insufficient stock this month")
        //     ])->setStatusCode(400);
        // }
        $data = [
            'item_id' => $request->item_id,
            'user_id' => $request->user_id,
            // 'quantity' => $request->quantity,
            'quantity' => $requestedQuantityInBaseUnit, // Jumlah dalam satuan input
            'date_out' => $request->date_out,
            'invoice_number' => $request->invoice_number ?? null,
            'supplier_id' => $request->supplier_id,
            // 'customer_id' => 0
        ];
        GoodsOut::create($data);
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = GoodsOut::with('supplier')->where('id', $id)->first();
        $barang = Item::with('category', 'unit', 'conversions.fromUnit', 'conversions.toUnit')->find($data->item_id);
        $data['kode_barang'] = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang'] = $barang->category->name;
        $data['nama_barang'] = $barang->name;
        $data['supplier_id'] = $data->supplier_id;
        $data['id_barang'] = $barang->id;
        // Tambahkan conversions dalam format array
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
        $data = GoodsOut::find($id);

        // Konversi quantity ke base unit
        $requestedQuantityInBaseUnit = $request->quantity / $request->conversionFactor;

        // Hitung total stok dalam base unit (sak)
        $totalStockInBaseUnit = (
            (Item::where('id', $request->item_id)->sum('quantity') +
                GoodsIn::where('item_id', $request->item_id)->sum('quantity') -
                GoodsOut::where('item_id', $request->item_id)->where('id', '!=', $id)->sum('quantity') - // Exclude current GoodsOut
                GoodsBack::where('item_id', $request->item_id)->sum('quantity')) +
            StockOpname::where('item_id', $request->item_id)->sum('quantity')
        );

        // Validasi stok
        if ($requestedQuantityInBaseUnit > $totalStockInBaseUnit) {
            return response()->json([
                "message" => __("Stok tidak mencukupi")
            ])->setStatusCode(400);
        }


        $data->user_id = $request->user_id;
        $data->supplier_id = $request->supplier_id;
        $data->date_out = $request->date_out;
        // $data->quantity = $request->quantity;
        $data->quantity = $requestedQuantityInBaseUnit; // Simpan dalam base unit
        $data->item_id = $request->item_id;
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
        $data = GoodsOut::find($id);
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
}
