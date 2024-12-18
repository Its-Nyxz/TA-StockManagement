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
use App\Models\StockOpname;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionOutController extends Controller
{
    public function index():View
    {
        $in_status = Item::where('active','true')->count();
        $customers = Customer::all();
        $users = User::all();
        $suppliers = Supplier::all();
        return view('admin.master.transaksi.keluar',compact('customers','in_status','users','suppliers'));
    }

    public function list(Request $request):JsonResponse
    {
        if( !empty($request->start_date) && !empty($request->end_date) ){
            $goodsouts = GoodsOut::with('item','user','supplier');
            $goodsouts -> whereBetween('date_out',[$request->start_date,$request->end_date]);
            if($request->inputer){
                $goodsouts -> where('user_id',[$request->inputer]);
            }
        }else if(!empty($request->inputer)){
            $goodsouts = GoodsOut::with('item','user','supplier');
            $goodsouts -> where('user_id',[$request->inputer]);
        }else{
            $goodsouts = GoodsOut::with('item','user','supplier');
        }
        if(Auth::user()->role->id > 2){
            $goodsouts -> where('user_id',Auth::user()->id);
        };
        $goodsouts ->latest()->get();
        if($request->ajax()){
            return DataTables::of($goodsouts)
            ->addColumn('quantity',function($data){
                $item = Item::with("unit")->find($data -> item -> id);
                return $data -> quantity ."/".$item -> unit -> name;
            })
            ->addColumn("date_out",function($data){
                return Carbon::parse($data->date_out)->format('d F Y');
            })
            ->addColumn("kode_barang",function($data){
                return $data -> item -> code;
            })
            // ->addColumn("costumer_name",function($data){
            //     return $data -> costumer -> name;
            // })
            ->addColumn("pemasok",function($data){
                return $data -> supplier -> name;
            })
            ->addColumn("brand",function($data){
                return $data -> item -> brand -> name;
            })

            ->addColumn("item_name",function($data){
                return $data -> item -> name;
            })
            ->addColumn('tindakan',function($data){
                $button = "<button class='ubah btn btn-success m-1' id='".$data->id."'><i class='fas fa-pen m-1'></i>".__("Edit")."</button>";
                $button .= "<button class='hapus btn btn-danger m-1' id='".$data->id."'><i class='fas fa-trash m-1'></i>".__("Delete")."</button>";
                return $button;
            })
            ->rawColumns(['tindakan'])
            -> make(true);
        }
    }

    public function save(Request $request):JsonResponse
    {

        // $currentMonth = date('m',strtotime($request->date_out));
        // $currentYear = date('Y',strtotime($request->date_out));
        // $goodsInThisMonth = GoodsIn::whereMonth('date_received', $currentMonth)
        // ->whereYear('date_received', $currentYear)->sum('quantity');
        // $goodsOutThisMonth = GoodsOut::whereMonth('date_out', $currentMonth)
        // ->whereYear('date_out', $currentYear)->sum('quantity');
        // $goodsBackThisMonth = GoodsBack::whereMonth('date_backs', $currentMonth)
        // ->whereYear('date_backs', $currentYear)->sum('quantity');
        $item = Item::where('id',$request->item_id)->sum('quantity');
        $goodsIn = GoodsIn::where('item_id',$request->item_id)->sum('quantity');
        $goodsOut = GoodsOut::where('item_id',$request->item_id)->sum('quantity');
        $goodsBack = GoodsBack::where('item_id',$request->item_id)->sum('quantity');
        $stockOpname = StockOpname::where('item_id',$request->item_id)->sum('quantity');

        $totalStock = max(0,$item + $goodsIn - $goodsOut - $goodsBack + $stockOpname);
        if($request->quantity > $totalStock || $totalStock === 0){
            return  response()->json([
                "message"=>__("insufficient stock this month")
            ]) -> setStatusCode(400);
        }
        $data = [
            'item_id'=>$request->item_id,
            'user_id'=>$request->user_id,
            'quantity'=>$request->quantity,
            'invoice_number'=>$request->invoice_number,
            'date_out'=>$request->date_out,
            'supplier_id'=>$request->supplier_id,
            'customer_id'=>1
        ];
        GoodsOut::create($data);
        return response() -> json([
            "message"=>__("saved successfully")
        ]) -> setStatusCode(200);
    }

    public function detail(Request $request):JsonResponse
    {
        $id = $request -> id;
        $data = GoodsOut::with('Customer')->where('id',$id)->first();
        $barang = Item::with('category','unit')->find($data -> item_id);
        $data['kode_barang'] = $barang -> code;
        $data['satuan_barang'] = $barang -> unit -> name;
        $data['jenis_barang'] = $barang -> category -> name;
        $data['nama_barang'] = $barang  -> name;
        $data['customer_id'] = $data -> customer_id;
        $data['supplier_id'] = $data -> supplier_id;
        $data['id_barang'] = $barang -> id;
        return response()->json(
            ["data"=>$data]
        )->setStatusCode(200);
    }

    public function update(Request $request):JsonResponse
    {
        $id = $request -> id;
        $data = GoodsOut::find($id);
        $data -> user_id = $request->user_id;
        $data -> customer_id = $request->customer_id;
        $data -> supplier_id = $request->supplier_id;
        $data -> date_out = $request->date_out;
        $data -> quantity = $request->quantity;
        $data -> item_id = $request->item_id;
        $status = $data -> save();
        if(!$status){
            return response()->json(
                ["message"=>__("data failed to change")]
            )->setStatusCode(400);
        }
        return response() -> json([
            "message"=>__("data changed successfully")
        ]) -> setStatusCode(200);
    }

    public function delete(Request $request):JsonResponse
    {
        $id = $request -> id;
        $data = GoodsOut::find($id);
        $status = $data -> delete();
        if(!$status){
            return response()->json(
                ["message"=>__("data failed to delete")]
            )->setStatusCode(400);
        }
        return response()->json([
            "message"=>__("data deleted successfully")
        ]) -> setStatusCode(200);
    }

}
