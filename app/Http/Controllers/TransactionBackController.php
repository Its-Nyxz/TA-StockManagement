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
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionBackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $in_status = Item::where('active', 'true')->count();
        $users = User::all();
        $suppliers = Supplier::all();
        $customers = Customer::all();
        return view('admin.master.transaksi.kembali', compact('users', 'in_status', 'suppliers', 'customers'));
    }

    public function list(Request $request): JsonResponse
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsbacks = GoodsBack::with('item', 'user');
            $goodsbacks->whereBetween('date_retur', [$request->start_date, $request->end_date]);
            if ($request->inputer) {
                $goodsbacks->where('user_id', [$request->inputer]);
            }
        } else if (!empty($request->inputer)) {
            $goodsbacks = GoodsBack::with('item', 'user');
            $goodsbacks->where('user_id', [$request->inputer]);
        } else {
            $goodsbacks = GoodsBack::with('item', 'user');
        }
        if (Auth::user()->role->id > 2) {
            $goodsbacks->where('user_id', Auth::user()->id);
        };
        $goodsbacks->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($goodsbacks)
                ->addColumn('quantity', function ($data) {
                    $item = Item::with("unit")->find($data->item->id);
                    return $data->quantity . "/" . $item->unit->name;
                })
                ->addColumn("date_backs", function ($data) {
                    return Carbon::parse($data->date_backs)->format('d F Y');
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
                ->addColumn("brand", function ($data) {
                    return $data->item->brand->name;
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

        $customerId = null;
        if ($request->return_type === 'customer') {
            if (!$request->customer_name) {
                return response()->json([
                    "message" => __("Customer name is required for customer return")
                ])->setStatusCode(400);
            }

            $slug = Str::slug($request->customer_name); // Buat slug dari nama input
            $customer = Customer::firstOrCreate(
                ['slug' => $slug], // Cari berdasarkan slug
                ['name' => $request->customer_name] // Buat pelanggan baru jika tidak ditemukan
            );
            $customerId = $customer->id;
        }

        $data = [
            'user_id' => $request->user_id,
            'date_backs' => $request->date_retur,
            'quantity' => $request->quantity,
            'description' => $request->description,
            'supplier_id' => $request->supplier_id,
            'customer_id' => $customerId,
            'invoice_number' => $request->invoice_number,
            'item_id' => $request->item_id
        ];
        // dd($data);
        GoodsBack::create($data);
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
        // Cari data retur dengan relasi user dan pelanggan
        $data = GoodsBack::with(['user', 'customer', 'supplier'])->where('id', $id)->first();
        // dd($data);
        $barang = Item::with('category', 'unit')->find($data->item_id);
        $data['kode_barang'] = $barang->code;
        $data['satuan_barang'] = $barang->unit->name;
        $data['jenis_barang'] = $barang->category->name;
        $data['nama_barang'] = $barang->name;
        $data['user_id'] = $data->user_id;
        $data['description'] = $data->description;
        $data['customer_name'] = $data->customer->name;
        $data['supplier_id'] = $data->supplier_id;
        $data['id_barang'] = $barang->id;
        return response()->json(
            ["data" => $data]
        )->setStatusCode(200);
    }

    public function update(Request $request): JsonResponse
    {
        $id = $request->id;
        $data = GoodsBack::find($id);

        // Logika untuk pelanggan jika tipe retur adalah customer
        $customerId = $data->customer_id; // Default jika tidak ada perubahan
        if ($request->return_type === 'customer') {
            if (!$request->customer_name) {
                return response()->json([
                    "message" => __("Customer name is required for customer return")
                ])->setStatusCode(400);
            }

            // Cari atau buat pelanggan baru berdasarkan slug
            $slug = Str::slug($request->customer_name);
            $customer = Customer::firstOrCreate(
                ['slug' => $slug], // Cari berdasarkan slug
                ['name' => $request->customer_name] // Data tambahan jika dibuat
            );
            $customerId = $customer->id;
        }


        $data->user_id = $request->user_id;
        $data->date_backs = $request->date_retur;
        $data->quantity = $request->quantity;
        $data->item_id = $request->item_id;
        $data->description = $request->description;
        $data->supplier_id = $request->supplier_id;
        $data->customer_id = $customerId;
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
        $data = GoodsBack::find($id);
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
        $items = Item::with('category', 'unit', 'brand')->where('active', 'true')->latest()->get();
        if ($request->ajax()) {
            return DataTables::of($items)
                ->addColumn('img', function ($data) {
                    if (empty($data->image)) {
                        return "<img src='" . asset('default.png') . "' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
                    }
                    return "<img src='" . asset('storage/barang/' . $data->image) . "' style='width:100%;max-width:240px;aspect-ratio:1;object-fit:cover;padding:1px;border:1px solid #ddd'/>";
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
                ->addColumn('tindakan', function ($data) {
                    $button = "<button class='ubah btn btn-success m-1' id='" . $data->id . "'>" . __("edit") . "</button>";
                    $button .= "<button class='hapus btn btn-danger m-1' id='" . $data->id . "'>" . __("delete") . "</button>";
                    return $button;
                })
                ->rawColumns(['img', 'tindakan'])
                ->make(true);
        }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsBack $goodsBack)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GoodsBack $goodsBack)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, GoodsBack $goodsBack)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsBack $goodsBack)
    {
        //
    }
}
