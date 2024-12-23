<?php

namespace App\Http\Controllers;

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
        return view('admin.master.transaksi.keluar', compact('customers', 'in_status', 'users', 'suppliers'));
    }

    public function list(Request $request): JsonResponse
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsouts = GoodsOut::with('item', 'user', 'supplier');
            $goodsouts->whereBetween('date_out', [$request->start_date, $request->end_date]);
            if ($request->inputer) {
                $goodsouts->where('user_id', [$request->inputer]);
            }
        } else if (!empty($request->inputer)) {
            $goodsouts = GoodsOut::with('item', 'user', 'supplier');
            $goodsouts->where('user_id', [$request->inputer]);
        } else {
            $goodsouts = GoodsOut::with('item', 'user', 'supplier');
        }
        if (Auth::user()->role->id > 2) {
            $goodsouts->where('user_id', Auth::user()->id);
        };
        $goodsouts->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($goodsouts)
                ->addColumn('quantity', function ($data) {
                    $item = Item::with("unit")->find($data->item->id);
                    return $data->quantity . "/" . $item->unit->name;
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

        // Ambil barang dan konversi satuan
        // $item = Item::find($request->item_id);
        $conversion = UnitConversion::where('item_id', $request->item_id)
            ->where(function ($query) use ($request) {
                $query->where('to_unit_id', $request->to_unit_id)
                    ->orWhere('from_unit_id', $request->to_unit_id);
            })
            ->first();

        if (!$conversion) {
            return response()->json([
                "message" => __("Conversion factor not found for the selected unit")
            ])->setStatusCode(400);
        }

        // Tentukan conversion_factor
        if ($conversion->to_unit_id == $request->to_unit_id) {
            $conversionFactor = $conversion->conversion_factor;
        } elseif ($conversion->from_unit_id == $request->to_unit_id) {
            $conversionFactor = 1 / $conversion->conversion_factor;
        } else {
            return response()->json([
                "message" => __("Invalid unit conversion")
            ])->setStatusCode(400);
        }

        // // Konversikan stok awal ke satuan terkecil (Kg)
        // $stokAwalKg = $item->quantity * $conversion->conversion_factor;

        // // Hitung jumlah transaksi dalam Kg
        // $transaksiKg = $request->quantity;

        // // Validasi stok
        // if ($transaksiKg > $stokAwalKg) {
        //     return response()->json([
        //         "message" => __("Insufficient stock for this transaction")
        //     ])->setStatusCode(400);
        // }

        // // Kurangi stok dalam Kg
        // $stokSisaKg = $stokAwalKg - $transaksiKg;

        // // Konversikan stok sisa kembali ke Sak
        // $stokSisaSak = $stokSisaKg / $conversion->conversion_factor;

        // // Perbarui stok barang
        // $item->quantity = $stokSisaSak; // Simpan dalam satuan Sak
        // $item->save();

        $item = Item::where('id', $request->item_id)->sum('quantity') * $conversionFactor;
        $goodsIn = GoodsIn::where('item_id', $request->item_id)->sum('quantity') * $conversion->conversion_factor;
        $goodsOut = GoodsOut::where('item_id', $request->item_id)->sum('quantity') * $conversion->conversion_factor;
        $goodsBack = GoodsBack::where('item_id', $request->item_id)->sum('quantity') * $conversion->conversion_factor;
        $stockOpname = StockOpname::where('item_id', $request->item_id)->sum('quantity') * $conversion->conversion_factor;
        // dd($conversion->conversion_factor);
        // $totalStock = max(0, $item + $goodsIn - $goodsOut - $goodsBack + $stockOpname);
        $totalStockSmallestUnit = max(0, $item + $goodsIn - $goodsOut - $goodsBack + $stockOpname);

        // Hitung jumlah dalam satuan terkecil
        $requestedQuantityInSmallestUnit = $request->quantity * $conversion->conversion_factor;
        // if ($request->quantity > $totalStock || $totalStock === 0) {
        //     return  response()->json([
        //         "message" => __("insufficient stock this month")
        //     ])->setStatusCode(400);
        // }
        // Validasi stok
        if ($requestedQuantityInSmallestUnit > $totalStockSmallestUnit) {
            return response()->json([
                "message" => __("Insufficient stock for this month")
            ])->setStatusCode(400);
        }
        $data = [
            'item_id' => $request->item_id,
            'user_id' => $request->user_id,
            // 'quantity' => $request->quantity,
            'quantity' => $request->quantity, // Jumlah dalam satuan input
            'conversion_factor' => $conversion->conversion_factor,
            'to_unit_id' => $request->to_unit_id, // Satuan tujuan
            'date_out' => $request->date_out,
            'invoice_number' => $request->invoice_number ?? null,
            'supplier_id' => $request->supplier_id,
            'customer_id' => 0
        ];
        GoodsOut::create($data);
        return response()->json([
            "message" => __("saved successfully")
        ])->setStatusCode(200);
    }

    public function detail(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = GoodsOut::with('Customer')->where('id', $id)->first();
        $barang = Item::with('category', 'unit')->find($data->item_id);
        $data['kode_barang'] = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang'] = $barang->category->name;
        $data['nama_barang'] = $barang->name;
        $data['customer_id'] = $data->customer_id;
        $data['supplier_id'] = $data->supplier_id;
        $data['id_barang'] = $barang->id;
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = GoodsOut::find($id);
        $data->user_id = $request->user_id;
        $data->customer_id = $request->customer_id;
        $data->supplier_id = $request->supplier_id;
        $data->date_out = $request->date_out;
        $data->quantity = $request->quantity;
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
