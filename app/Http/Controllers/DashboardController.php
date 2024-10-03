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
        $item_in = GoodsIn::sum('quantity');
        $item_out = GoodsOut::sum('quantity');
        $total_stok = $item_in - $item_out;
        $staffCount = User::where('role_id',2)->count();
        return view('admin.dashboard',compact('product_count',
        'category_count','unit_count',
        'brand_count','goodsin','goodsout','goodsback','customer','supplier','staffCount','total_stok'));
    }
}
