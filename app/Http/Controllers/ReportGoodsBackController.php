<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\GoodsBack;
use App\Models\Item;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class ReportGoodsBackController extends Controller
{
    public function index(): View
    {
        return view('admin.master.laporan.kembali');
    }

    public function list(Request $request):JsonResponse
    {
        if($request->ajax()){
            if( empty($request->start_date) && empty($request->end_date)){
                $GoodsBacks = GoodsBack::with('item','user','supplier');
            }else{
                $GoodsBacks = GoodsBack::with('item','user','supplier');
                $GoodsBacks -> whereBetween('date_backs',[$request->start_date,$request->end_date]);
            }
            $GoodsBacks -> latest() -> get();
            return DataTables::of($GoodsBacks)
            ->addColumn('quantity',function($data){
                $item = Item::with("unit")->find($data -> item -> id);
                return $data -> quantity ."/".$item -> unit -> name;
            })
            ->addColumn("date_backs",function($data){
                return Carbon::parse($data->date_backs)->format('d F Y');
            })
            ->addColumn("kode_barang",function($data){
                return $data -> item -> code;
            })
            ->addColumn("supplier_name",function($data){
                return $data -> item-> supplier -> name;
            })
            ->addColumn("item_name",function($data){
                return $data -> item -> name;
            })
            ->addColumn("brand",function($data){
                return $data -> item ->brand -> name;
            })
            -> make(true);
        }
    }
}
