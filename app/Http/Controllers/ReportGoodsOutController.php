<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Brand;
use App\Models\GoodsOut;
use App\Models\Supplier;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;

class ReportGoodsOutController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        $brands = Brand::all();
        return view('admin.master.laporan.keluar', compact('suppliers', 'brands'));
    }

    public function list(Request $request): JsonResponse
    {
        // Query dasar dengan relasi yang diperlukan
        $goodsouts = GoodsOut::with('item.brand', 'user', 'supplier');

        // Filter berdasarkan rentang tanggal
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsouts->whereBetween('date_out', [$request->start_date, $request->end_date]);
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
        $goodsouts->latest()->get();
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
            ->addColumn("brand", function ($data) {
                return $data->item->brand->name;
            })
            ->addColumn("supplier", function ($data) {
                return $data->supplier->name;
            })
            ->addColumn("item_name", function ($data) {
                return $data->item->name;
            })
            ->make(true);
    }
}
