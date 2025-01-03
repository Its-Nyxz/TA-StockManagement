<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\GoodsBack;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;

class ReportGoodsBackController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::all();
        $brands = Brand::all();
        return view('admin.master.laporan.kembali', compact('suppliers', 'brands'));
    }

    public function list(Request $request): JsonResponse
    {
        // Query dasar dengan relasi yang diperlukan
        $goodsbacks = GoodsBack::with('item.brand', 'user', 'supplier');

        // Filter berdasarkan rentang tanggal
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $goodsbacks->whereBetween('date_backs', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan user inputer
        if (!empty($request->inputer)) {
            $goodsbacks->where('user_id', $request->inputer);
        }

        // Filter berdasarkan supplier
        if (!empty($request->suppliers)) {
            $goodsbacks->where('supplier_id', $request->suppliers);
        }

        // Filter berdasarkan brand
        if (!empty($request->brands)) {
            $goodsbacks->whereHas('item.brand', function ($query) use ($request) {
                $query->where('id', $request->brands);
            });
        }

        // Filter berdasarkan item_name
        if (!empty($request->item_name)) {
            $goodsbacks->whereHas('item', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->item_name . '%');
            });
        }
        $goodsbacks->latest()->get();
        return DataTables::of($goodsbacks)
            ->addColumn('quantity', function ($data) {
                // Pastikan relasi unit sudah dimuat sebelumnya
                $unitName = $data->item->unit->name ?? '';
                return number_format($data->quantity, 2) . " / " . $unitName;
            })
            ->addColumn("date_backs", function ($data) {
                return Carbon::parse($data->date_backs)->format('d F Y');
            })
            ->addColumn("kode_barang", function ($data) {
                return $data->item->code;
            })
            ->addColumn("supplier_name", function ($data) {
                return $data->item->supplier->name;
            })
            ->addColumn("item_name", function ($data) {
                return $data->item->name;
            })
            ->addColumn("brand", function ($data) {
                return $data->item->brand->name;
            })
            ->make(true);
    }
}
