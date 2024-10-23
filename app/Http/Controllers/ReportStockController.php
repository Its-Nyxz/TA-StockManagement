<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ReportStockController extends Controller
{
    public function index(): View
    {
        return view('admin.master.laporan.stok');
    }

    public function list(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            if (empty($request->start_date) && empty($request->end_date)) {
                $data = Item::with('goodsOuts', 'goodsIns', 'goodsBacks','stockOpnames');
            } else {
                $data = Item::with(['goodsOuts' => function ($query) use ($request) {
                    $query->whereBetween('date_out', [$request->start_date, $request->end_date]);
                }, 'goodsIns'  => function ($query) use ($request) {
                    $query->whereBetween('date_received', [$request->start_date, $request->end_date]);
                }, 'goodsBacks'  => function ($query) use ($request) {
                    $query->whereBetween('date_backs', [$request->start_date, $request->end_date]);
                }]);
            }
            $data->latest()->get();
            return DataTables::of($data)
                ->addColumn('jumlah_masuk', function ($item) {
                    $totalQuantity = $item->goodsIns->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("jumlah_keluar", function ($item) {
                    $totalQuantity = $item->goodsOuts->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("jumlah_retur", function ($item) {
                    $totalQuantity = $item->goodsBacks->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    return $totalQuantity . "/" . $data->unit->name;
                })
                ->addColumn("kode_barang", function ($item) {
                    return $item->code;
                })
                ->addColumn("stok_awal", function ($item) {
                    $data = Item::with("unit")->find($item->id);
                    return $item->quantity . "/" . $data->unit->name;
                })
                ->addColumn("nama_barang", function ($item) {
                    return $item->name;
                })
                ->addColumn("total", function ($item) {
                    $totalQuantityIn = $item->goodsIns->sum('quantity');
                    $totalQuantityOut = $item->goodsOuts->sum('quantity');
                    $totalQuantityRetur = $item->goodsBacks->sum('quantity');
                    $totalQuantitySO = $item->stockOpnames->sum('quantity');
                    $data = Item::with("unit")->find($item->id);
                    // if ($item->stockOpnames->status === 'plus') {
                    //     $count = $item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur + $totalQuantitySO;
                    // }elseif($item->stockOpnames->status === 'min'){
                    //     $count = $item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur - $totalQuantitySO;
                    // }else{
                    //     $count = $item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur;
                    // }
                    $count = $item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur;
                    $result = $count. "/" . $data->unit->name;
                    $result = max(0, $result);
                    if ($count <= 0) {
                        return "<span class='text-red font-weight-bold'>" . $result . "</span>" . ' ' . "<span class='badge badge-danger'>" . __("Stock Empty") . "</span>";;
                    }else if ($count <= 10) {
                        return "<span class='text-red font-weight-bold'>" . $result . "</span>" . ' ' . "<span class='badge badge-danger'>" . __("Stock Running Low") . "</span>";;
                    }else{
                        return  "<span class='text-success font-weight-bold'>" . $result . "</span>";
                    }

                })
                ->rawColumns(['total'])
                ->make(true);
        }
    }

    public function grafik(Request $request): JsonResponse
    {
        if ($request->has('month') && !empty($request->month)) {
            $month = $request->month;
            $currentMonth = preg_split("/[-\s]/", $month)[1];
            $currentYear = preg_split("/[-\s]/", $month)[0];
        } else {
            $currentMonth = date('m');
            $currentYear = date('Y');
        }
        $goodsInThisMonth = GoodsIn::whereMonth('date_received', $currentMonth)
            ->whereYear('date_received', $currentYear)->sum('quantity');
        $goodsOutThisMonth = GoodsOut::whereMonth('date_out', $currentMonth)
            ->whereYear('date_out', $currentYear)->sum('quantity');
        $goodsBackThisMonth = GoodsBack::whereMonth('date_backs', $currentMonth)
            ->whereYear('date_backs', $currentYear)->sum('quantity');
        $totalStockThisMonth = max(0, $goodsInThisMonth - $goodsOutThisMonth - $goodsBackThisMonth);
        return response()->json([
            'month' => $currentYear . '-' . $currentMonth,
            'goods_in_this_month' => $goodsInThisMonth,
            'goods_out_this_month' => $goodsOutThisMonth,
            'goods_back_this_month' => $goodsBackThisMonth,
            'total_stock_this_month' => $totalStockThisMonth,
        ]);
    }

    public function pietoday(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today());

        $today = Carbon::parse($date);

        $goodsInToday = GoodsIn::whereDate('date_received', $today)->sum('quantity') ?? 0;
        $goodsOutToday = GoodsOut::whereDate('date_out', $today)->sum('quantity') ?? 0;
        $goodsBackToday = GoodsBack::whereDate('date_backs', $today)->sum('quantity') ?? 0;
        $totalStockToday = max(0, $goodsInToday - $goodsOutToday - $goodsBackToday);

        return response()->json([
            'goods_in_today' => $goodsInToday,
            'goods_out_today' => $goodsOutToday,
            'goods_back_today' => $goodsBackToday,
            'goods_total_today' => $totalStockToday,
        ]);
    }
}
