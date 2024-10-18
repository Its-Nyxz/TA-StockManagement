<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Role;
use App\Models\User;
use App\Models\GoodsIn;
use App\Models\GoodsOut;
use App\Models\GoodsBack;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {

        $product_count = Item::count();
        $category_count = Category::count();
        $unit_count = Unit::count();
        $brand_count = Brand::count();
        $goodsin = GoodsIn::count();
        $goodsout = GoodsOut::count();
        $goodsback = GoodsBack::count();
        $customer = Customer::count();
        $supplier = Supplier::count();
        $item = Item::sum('quantity');
        $item_in = GoodsIn::sum('quantity');
        $item_out = GoodsOut::sum('quantity');
        $item_back = GoodsBack::sum('quantity');
        $total_stok = $item + $item_in - $item_out - $item_back;
        $staffCount = User::where('role_id', 3)->count();
        $approvals = GoodsIn::with('item', 'supplier')->where('status', 0)->get();
        $get_item = Item::orderBy('id', 'DESC')->get();
        $get_item_sum = Item::with(['goodsIns', 'goodsOuts', 'goodsBacks'])
            ->get()
            ->filter(function ($item) {
                $total_stok = $item->quantity + $item->goodsIns->sum('quantity')
                    - $item->goodsOuts->sum('quantity')
                    - $item->goodsBacks->sum('quantity');
                return $total_stok >= 10 && $total_stok <= 50;;
            });
        $get_goodsIns = GoodsIn::with('item', 'user', 'supplier');
        if (Auth::user()->role->id > 2) {
            $get_goodsIns->where('user_id', Auth::user()->id);
        }; 
        $get_goodsIns->where('status','!=','2')->whereDate('date_received',Carbon::now());
        $get_goodsIns = $get_goodsIns->latest()->get();
        return view('admin.dashboard', compact(
            'product_count',
            'category_count',
            'unit_count',
            'brand_count',
            'goodsin',
            'goodsout',
            'goodsback',
            'customer',
            'supplier',
            'staffCount',
            'total_stok',
            'approvals',
            'get_item',
            'get_item_sum',
            'get_goodsIns'
        ));
    }
}
