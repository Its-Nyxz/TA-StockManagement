<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Brand;
use App\Models\GoodsIn;
use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;

class ReportGoodsInController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        $brands = Brand::all();
        return view('admin.master.laporan.masuk', compact('suppliers', 'brands'));
    }

    public function list(Request $request): JsonResponse
    {
        $goodsins = GoodsIn::with('item', 'user', 'supplier');
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsins->whereBetween('date_received', [$request->start_date, $request->end_date]);
        }

        if (isset($request->status)) {
            $goodsins->where('status', $request->status);
        }

        // Filter berdasarkan supplier
        if (!empty($request->suppliers)) {
            $goodsins->where('supplier_id', $request->suppliers);
        }

        // Filter berdasarkan brand
        if (!empty($request->brands)) {
            $goodsins->whereHas('item.brand', function ($query) use ($request) {
                $query->where('id', $request->brands);
            });
        }

        // Filter berdasarkan item_name
        if (!empty($request->item_name)) {
            $goodsins->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->item_name . '%');
            });
        }

        $goodsins->latest()->get();
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
                    } else if ($data->status == 1) {
                        return "<span class='badge badge-success'>" . __("approved") . "</span>";
                    } else {
                        return "<span class='badge badge-danger'>" . __("retur") . "</span>";
                    }
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }
}
