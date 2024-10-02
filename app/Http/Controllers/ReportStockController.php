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

class ReportStockController extends Controller
{
    public function index():View
    {
        return view('admin.master.laporan.stok');
    }

    public function list(Request $request):JsonResponse
    {
        if($request->ajax()){
            if( empty($request->start_date) && empty($request->end_date)){
                $data = Item::with('goodsOuts','goodsIns','goodsBacks');
            }else{
                $data = Item::with(['goodsOuts' => function ($query) use ($request){
                    $query -> whereBetween('date_out',[$request->start_date,$request->end_date]);
                },'goodsIns'  => function ($query) use ($request){
                    $query -> whereBetween('date_received',[$request->start_date,$request->end_date]);
                },'goodsBacks'  => function ($query) use ($request){
                    $query -> whereBetween('date_backs',[$request->start_date,$request->end_date]);
                }]);
            }
            $data -> latest() -> get();
            return DataTables::of($data)
            ->addColumn('jumlah_masuk', function ($item) {
                $totalQuantity = $item->goodsIns->sum('quantity');
                $data = Item::with("unit")->find($item -> id);
                return $totalQuantity."/".$data -> unit -> name ;
            })
            ->addColumn("jumlah_keluar", function ($item) {
                $totalQuantity = $item->goodsOuts->sum('quantity');
                $data = Item::with("unit")->find($item -> id);
                return $totalQuantity."/".$data -> unit -> name ;
            })
            ->addColumn("jumlah_retur", function ($item) {
                $totalQuantity = $item->goodsBacks->sum('quantity');
                $data = Item::with("unit")->find($item -> id);
                return $totalQuantity."/".$data -> unit -> name ;
            })
            ->addColumn("kode_barang", function ($item) {
                return $item->code;
            })
            ->addColumn("stok_awal", function ($item) {
                $data = Item::with("unit")->find($item -> id);
                return $item->quantity ."/". $data -> unit -> name;
            })
            ->addColumn("nama_barang", function ($item) {
                return $item->name;
            })
            ->addColumn("total", function ($item) {
                $totalQuantityIn = $item->goodsIns->sum('quantity');
                $totalQuantityOut = $item->goodsOuts->sum('quantity');
                $totalQuantityRetur = $item->goodsBacks->sum('quantity');
                $data = Item::with("unit")->find($item -> id);
                $result = $item->quantity + $totalQuantityIn - $totalQuantityOut - $totalQuantityRetur."/". $data -> unit -> name;
                $result = max(0, $result);
                if($result == 0){
                    return "<span class='text-red font-weight-bold'>".$result."</span>";
                }
                return  "<span class='text-success font-weight-bold'>".$result."</span>";
            })
            ->rawColumns(['total'])
            ->make(true);
        }
    }

    public function grafik(Request $request): JsonResponse
    {
        if($request->has('month') && !empty($request->month) ){
            $month = $request->month;
            $currentMonth = preg_split("/[-\s]/", $month)[1];
            $currentYear = preg_split("/[-\s]/", $month)[0];
        }else{
            $currentMonth = date('m');
            $currentYear = date('Y');
        }
        $goodsInThisMonth = GoodsIn::whereMonth('date_received', $currentMonth)
        ->whereYear('date_received', $currentYear)->sum('quantity');
        $goodsOutThisMonth = GoodsOut::whereMonth('date_out', $currentMonth)
        ->whereYear('date_out', $currentYear)->sum('quantity');
        $totalStockThisMonth = max(0,$goodsInThisMonth - $goodsOutThisMonth);
        return response()->json([
            'month'=>$currentYear.'-'.$currentMonth,
            'goods_in_this_month' => $goodsInThisMonth,
            'goods_out_this_month' => $goodsOutThisMonth,
            'total_stock_this_month' => $totalStockThisMonth,
        ]);
    }
}
