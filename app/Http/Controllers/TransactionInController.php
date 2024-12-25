<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Item;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class TransactionInController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $approvals = GoodsIn::with('item', 'supplier')->where('status', 0)->get();
        return view('admin.master.transaksi.masuk', compact('suppliers', 'users', 'approvals'));
    }

    public function list(Request $request): JsonResponse
    {
        $goodsins = GoodsIn::with('item', 'user', 'supplier');

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsins->whereBetween('date_received', [$request->start_date, $request->end_date]);
        }

        if (!empty($request->inputer)) {
            $goodsins->where('user_id', $request->inputer);
        }

        if (isset($request->status)) {
            $goodsins->where('status', $request->status);
        }

        if (Auth::user()->role->id > 2) {
            $goodsins->where('user_id', Auth::user()->id);
        };

        $goodsins->where('status', '!=', '2');
        $goodsins->latest()->get();
        // $goodsins = GoodsIn::with('item','user','supplier')->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($goodsins)
                ->addColumn('quantity', function ($data) {
                    // Pastikan relasi unit sudah dimuat sebelumnya
                    $unitName = $data->item->unit->name ?? '';
                    return number_format($data->quantity, 2) . " / " . $unitName;
                })
                ->addColumn("date_received", function ($data) {
                    return Carbon::parse($data->date_received)->format('d F Y');
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
                ->addColumn("brand_name", function ($data) {
                    return $data->item->brand->name;
                })

                ->addColumn("status", function ($data) {
                    if ($data->status == 0) {
                        return "<span class='badge badge-warning'>" . __("pending") . "</span>";
                    } else {
                        return "<span class='badge badge-success'>" . __("approved") . "</span>";
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
        $data = [
            'user_id' => $request->user_id,
            'supplier_id' => $request->supplier_id,
            'date_received' => $request->date_received,
            'quantity' => $request->quantity,
            'invoice_number' => $request->invoice_number,
            'item_id' => $request->item_id,
            // 'status' => Auth::user()->role->id == 3 ? 0 : 1
            'status' => 0
        ];
        GoodsIn::create($data);
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
        $data = GoodsIn::with('supplier')->where('id', $id)->first();
        $barang = Item::with('category', 'unit')->find($data->item_id);
        $data['kode_barang'] = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang'] = $barang->category->name;
        $data['nama_barang'] = $barang->name;
        $data['supplier_id'] = $data->supplier_id;
        $data['id_barang'] = $barang->id;
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = GoodsIn::find($id);
        $data->user_id = $request->user_id;
        $data->supplier_id = $request->supplier_id;
        $data->date_received = $request->date_received;
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
        $data = GoodsIn::find($id);
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

    public function listIn(Request $request): JsonResponse
    {
        // $items = Item::with('category', 'unit', 'brand')->where('active', 'false')->latest()->get();
        // if ($request->ajax()) {
        //     return DataTables::of($items)
        //         ->addColumn('img', function ($data) {
        //             if (empty($data->image)) {
        //                 return "<img src='" . asset('default.png') . "' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
        //             }
        //             return "<img src='" . asset('storage/barang/' . $data->image) . "' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
        //         })
        //         ->addColumn('category_name', function ($data) {
        //             return $data->category->name;
        //         })
        //         ->addColumn('unit_name', function ($data) {
        //             return $data->unit->name;
        //         })
        //         ->addColumn('brand_name', function ($data) {
        //             return $data->brand->name;
        //         })
        //         ->addColumn('tindakan', function ($data) {
        //             $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'>" . __("edit") . "</button>";
        //             $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'>" . __("delete") . "</button>";
        //             return $button;
        //         })
        //         ->rawColumns(['img', 'tindakan'])
        //         ->make(true);
        // }

        $items = Item::with('category', 'unit', 'brand', 'supplier', 'goodsIns', 'goodsOuts', 'goodsBacks', 'stockOpnames')->where('supplier_id', $request->supplier_id)->latest()->get();
        // dd($items);
        if ($request->ajax()) {
            return DataTables::of($items)
                ->addColumn('img', function ($data) {
                    $imageUrl = empty($data->image)
                        ? asset('default.png')
                        : asset('storage/barang/' . $data->image);

                    return "<img src='" . $imageUrl . "' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
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

                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'><i class='fas fa-pen m-1'></i>" . __("edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'><i class='fas fa-trash m-1'></i>" . __("delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['img', 'tindakan'])
                ->make(true);
        }
    }

    public function approve($id)
    {
        // Find the transaction by ID and update its status
        $transaction = GoodsIn::find($id); // Ensure you have the correct model

        if ($transaction) {
            $transaction->status = '1';
            $transaction->save();

            return response()->json(['success' => true, 'message' => __('Transaction approved successfully.')]);
        }

        return response()->json(['success' => false, 'message' => __('Transaction not found.')]);
    }

    public function cancel(Request $request, $id)
    {
        $transaction = GoodsIn::find($id);
        if ($transaction) {
            $transaction->status = '2';

            $item = Item::where('id', $request->item_id)->sum('quantity');
            $goodsIn = GoodsIn::where('item_id', $request->item_id)->sum('quantity');
            $goodsOut = GoodsOut::where('item_id', $request->item_id)->sum('quantity');
            $goodsBack = GoodsBack::where('item_id', $request->item_id)->sum('quantity');

            $totalStock = max(0, $item + $goodsIn - $goodsOut - $goodsBack);
            if ($request->quantity > $totalStock || $totalStock === 0) {
                return  response()->json([
                    "message" => __("insufficient stock this month")
                ])->setStatusCode(400);
            }
            $transaction->save();
            $data = [
                'user_id' => $request->user_id,
                'date_backs' => $request->date_retur,
                'quantity' => $request->quantity,
                'description' => $request->description,
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'item_id' => $request->item_id
            ];

            // Create the GoodsBack record
            GoodsBack::create($data);

            // Optionally update the item's active status
            $item = Item::find($request->item_id);
            if ($item) {
                $item->active = "true"; // Set active status
                $item->save();
            }

            return response()->json(['success' => true, 'message' => __('Transaction Cancel or Returned successfully.')]);
        }

        return response()->json(['success' => false, 'message' => __('Transaction not found.')]);
    }

    public function modal(Request $request)
    {
        session(['show_modal' => true]);

        return redirect()->route('transaksi.masuk');
    }
}
